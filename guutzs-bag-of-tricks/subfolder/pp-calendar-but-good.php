<?php

/** https://studentblog.caltech.edu
 * ?user=ugadmissionscaltechblog
 * &user_key=super-secret-password
 * &pp_action=blog_calendar_request
 */

add_action('template_include', 'handle_blog_calendar_request');

/**
* @param $original_template
*
* @return mixed
*/
function handle_blog_calendar_request($original_template)
{

    // Confirm all of the arguments are present
    if (! isset($_GET['user'], $_GET['user_key'], $_GET['pp_action'])) {
        return $original_template;
    }

    // Confirm the action
    if ('blog_calendar_request' !== $_GET['pp_action']) {
        return $original_template;
    }

    create_ics();

}


function create_ics()
{
    $dateUtil = new \PublishPress\Utility\Date();
    // Confirm all the arguments are present
    if (! isset($_GET['user'], $_GET['user_key'])) {
        die();
    }

    // Confirm this is a valid request
    $user = sanitize_user($_GET['user']);
    $user_key = sanitize_user($_GET['user_key']);
    $ics_secret_key = "super-secret-password";
    if ($ics_secret_key !== $user_key) {
        die(esc_html('Invalid request'));
    }

    // Set up the post data to be printed
    $post_query_args = [
        'post_type' => 'post',
        'post_status' => array('future', 'draft', 'draft-completed', 'pending', 'publish'),
        'posts_per_page' => -1,
        'date_query' => array(
            'after' => date('Y-m-d', strtotime('-1 month')),
        ),
    ];

    $vCalendar = new Sabre\VObject\Component\VCalendar(
        [
            'PRODID' => '-//PublishPress//PublishPress ' . PUBLISHPRESS_VERSION . '//EN',
        ]
    );

    $query = new WP_Query($post_query_args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $start_date = new DateTime(get_post_time('Ymd'));
            $start_date = $start_date->format('Ymd');
            $end_date = new DateTime(get_post_time('Ymd') . '+1 day');
            $end_date = $end_date->format('Ymd');

            // Remove the convert chars and wptexturize filters from the title
            remove_filter('the_title', 'convert_chars');
            remove_filter('the_title', 'wptexturize');

            // Description should include everything visible in the calendar popup
            $information_fields = get_post_information_fields(get_post());
            $eventDescription = '';
            $categories = '';
            $author = get_the_author_meta('display_name', get_the_author_meta('ID'));
            $author_initials = preg_replace('/[^A-Z]+/', '', $author);
            if (!empty($information_fields)) {
                foreach ($information_fields as $key => $values) {
                    $eventDescription .= $values['label'] . ': ' . $values['value'] . "\n";
                    if ($values['label'] == 'Categories') {
                        $categories = $values['value'];
                    }
                }
                $eventDescription = get_edit_post_link() . PHP_EOL . rtrim($eventDescription);
            }

            $vCalendar->add(
                'VEVENT',
                [
                    'UID' => get_the_guid(),
                    'SUMMARY' => $author_initials . ' | ' . $categories . ' | '
                    . get_post_status_friendly_name(get_post_status()) . ' | '
                    . do_ics_escaping(apply_filters('the_title', get_the_title())),
                    'DTSTART;VALUE=DATE' => $start_date,
                    'DTEND;VALUE=DATE' => $end_date,
                    'URL' => get_edit_post_link(),
                    'DESCRIPTION' => $eventDescription,
                    'X-MICROSOFT-CDO-ALLDAYEVENT' => 'TRUE',
                    'X-MICROSOFT-CDO-BUSYSTATUS' => 'FREE',
                    'X-MICROSOFT-CDO-INTENDEDSTATUS' => 'FREE',
                    'TRANSP' => 'TRANSPARENT',
                ]
            );
        }
    }

    wp_reset_postdata();

    // Render the .ics template and set the content type
    header('Content-type: text/calendar; charset=utf-8');
    header('Content-Disposition: inline; filename=calendar.ics');
    echo $vCalendar->serialize();

    die();
    // phpcs:enable
}

/**
 * Get the information fields to be presented with each post popup
 *
 * @param obj $post Post to gather information fields for
 *
 * @return array $information_fields All of the information fields to be presented
 * @since 0.8
 *
 */
function get_post_information_fields($post)
{
    $information_fields = [];
    // Post author
    $authorsNames = apply_filters(
        'publishpress_post_authors_names',
        [get_the_author_meta('display_name', $post->post_author)],
        $post->ID
    );

    $information_fields['author'] = [
        'label' => _n('Author', 'Authors', count($authorsNames), 'publishpress'),
        'value' => implode(', ', $authorsNames),
        'type' => 'author',
    ];

    // Publication time
    $information_fields['post_date'] = [
        'label' => __('Scheduled', 'publishpress'),
        'value' => get_the_time(null, $post->ID),
    ];

    // Taxonomies and their values
    $args = [
        'post_type' => $post->post_type,
    ];
    $taxonomies = get_object_taxonomies($args, 'object');
    foreach ((array)$taxonomies as $taxonomy) {
        // Sometimes taxonomies skip by, so let's make sure it has a label too
        if (! $taxonomy->public || ! $taxonomy->label) {
            continue;
        }

        $terms = get_the_terms($post->ID, $taxonomy->name);
        if (! $terms || is_wp_error($terms)) {
            continue;
        }

        $key = 'tax_' . $taxonomy->name;
        if (count($terms)) {
            $value = '';
            foreach ((array)$terms as $term) {
                $value .= $term->name . ', ';
            }
            $value = rtrim($value, ', ');
        } else {
            $value = '';
        }
        // Used when editing editorial metadata and post meta
        if (is_taxonomy_hierarchical($taxonomy->name)) {
            $type = 'taxonomy hierarchical';
        } else {
            $type = 'taxonomy';
        }

        $information_fields[$key] = [
            'label' => $taxonomy->label,
            'value' => $value,
            'type' => $type,
        ];

        if ($post->post_type == 'page') {
            $ed_cap = 'edit_page';
        } else {
            $ed_cap = 'edit_post';
        }

        if (current_user_can($ed_cap, $post->ID)) {
            $information_fields[$key]['editable'] = true;
        }
    }

    return $information_fields;
}

/**
 * Perform the encoding necessary for ICS feed text.
 *
 * @param string $text The string that needs to be escaped
 *
 * @return string The string after escaping for ICS.
 * @since 0.8
 * */

function do_ics_escaping($text)
{
    $text = str_replace(',', '\,', $text);
    $text = str_replace(';', '\:', $text);
    $text = str_replace('\\', '\\\\', $text);

    return $text;
}

/**
 * Returns the friendly name for a given status
 *
 * @param string $status The status slug
 *
 * @return string $status_friendly_name The friendly name for the status
 * @since 0.7
 *
 */
function get_post_status_friendly_name($status)
{
    global $publishpress;

    $status_friendly_name = '';

    $builtin_stati = [
        'publish' => __('Published', 'publishpress'),
        'draft'   => __('Draft', 'publishpress'),
        'future'  => __('Scheduled', 'publishpress'),
        'private' => __('Private', 'publishpress'),
        'pending' => __('Pending Review', 'publishpress'),
        'trash'   => __('Trash', 'publishpress'),
    ];

    // Custom statuses only handles workflow statuses
    if (!in_array($status, ['publish', 'future', 'private', 'trash'])) {
        $status_object = $publishpress->getPostStatusBy('slug', $status);

        if ($status_object && !is_wp_error($status_object)) {
            $status_friendly_name = $status_object->label;
        }
    } elseif (array_key_exists($status, $builtin_stati)) {
        $status_friendly_name = $builtin_stati[$status];
    }

    return $status_friendly_name;
}