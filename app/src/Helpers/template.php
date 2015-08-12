<?php

use EBloodBank\EntityManager;

/**
 * @return string
 * @since 1.0
 */
function getLoginURL()
{
    $url = getSiteURL('/login');
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getLogoutURL()
{
    $url = getLoginURL();
    $url = addQueryArgs($url, array(
        'action' => 'logout',
    ));
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getSignupURL()
{
    $url = getSiteURL('/signup');
    return $url;
}

/*** Users Template Tags ******************************************************/

/**
 * @return string
 * @since 1.0
 */
function getUsersURL()
{
    $url = getSiteURL('/users');
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getAddUserURL()
{
    $url = getSiteURL('/add/user');
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getEditUsersURL()
{
    $url = getSiteURL('/edit/users');
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getEditUserURL($id)
{
    $url = '';
    $id = (int) $id;

    if (! isVaildID($id)) {
        return $url;
    }

    $url = getSiteURL("/edit/user/{$id}");
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getDeleteUserURL($id)
{
    $url = '';
    $id = (int) $id;

    if (! isVaildID($id)) {
        return $url;
    }

    $url = addQueryArgs(getEditUsersURL(), array(
        'action' => 'delete_user',
        'id' => $id,
    ));

    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getApproveUserURL($id)
{
    $url = '';
    $id = (int) $id;

    if (! isVaildID($id)) {
        return $url;
    }

    $url = addQueryArgs(getEditUsersURL(), array(
        'action' => 'approve_user',
        'id' => $id,
    ));

    return $url;
}


/**
 * @return string
 * @since 1.0
 */
function getUsersLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'content' => __('Users'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isCurrentUserCan('view_users')) {
        return $link;
    }

    $args['atts']['href'] = getUsersURL();

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'view-link view-users-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getAddUserLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'content' => __('Add'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isCurrentUserCan('add_user')) {
        return $link;
    }

    $args['atts']['href'] = getAddUserURL();

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'add-link add-user-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getEditUsersLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'content' => __('Edit'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isCurrentUserCan('edit_users')) {
        return $link;
    }

    $args['atts']['href'] = getEditUsersURL();

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'edit-link edit-users-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getEditUserLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'id' => 0,
        'content' => __('Edit'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isVaildID($args['id'])) {
        return $link;
    }

    if (! isCurrentUserCan('edit_user')) {
        return $link;
    }

    $args['atts']['href'] = getEditUserURL($args['id']);

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'edit-link edit-user-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getDeleteUserLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'id' => 0,
        'content' => __('Delete'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isVaildID($args['id'])) {
        return $link;
    }

    if (! isCurrentUserCan('delete_user')) {
        return $link;
    }

    if ($args['id'] === getCurrentUserID()) {
        return $link;
    }

    $args['atts']['href'] = getDeleteUserURL($args['id']);

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'delete-link delete-user-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getApproveUserLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'id' => 0,
        'content' => __('Approve'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isVaildID($args['id'])) {
        return $link;
    }

    if (! isCurrentUserCan('approve_user')) {
        return $link;
    }

    $userRepository = EntityManager::getUserRepository();
    $user = $userRepository->find($args['id']);

    if ('pending' !== $user->get('status')) {
        return $link;
    }

    $args['atts']['href'] = getApproveUserURL($args['id']);

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'approve-link approve-user-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/*** Donors Template Tags *****************************************************/

/**
 * @return string
 * @since 1.0
 */
function getDonorsURL()
{
    $url = getSiteURL('/donors');
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getAddDonorURL()
{
    $url = getSiteURL('/add/donor');
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getEditDonorsURL()
{
    $url = getSiteURL('/edit/donors');
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getEditDonorURL($id)
{
    $url = '';
    $id = (int) $id;

    if (! isVaildID($id)) {
        return $url;
    }

    $url = getSiteURL("/edit/donor/{$id}");
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getDeleteDonorURL($id)
{
    $url = '';
    $id = (int) $id;

    if (! isVaildID($id)) {
        return $url;
    }

    $url = addQueryArgs(getEditDonorsURL(), array(
        'action' => 'delete_donor',
        'id' => $id,
    ));

    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getApproveDonorURL($id)
{
    $url = '';
    $id = (int) $id;

    if (! isVaildID($id)) {
        return $url;
    }

    $url = addQueryArgs(getEditDonorsURL(), array(
        'action' => 'approve_donor',
        'id' => $id,
    ));

    return $url;
}


/**
 * @return string
 * @since 1.0
 */
function getDonorsLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'content' => __('Donors'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isCurrentUserCan('view_donors')) {
        return $link;
    }

    $args['atts']['href'] = getDonorsURL();

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'view-link view-donors-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getAddDonorLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'content' => __('Add'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isCurrentUserCan('add_donor')) {
        return $link;
    }

    $args['atts']['href'] = getAddDonorURL();

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'add-link add-donor-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getEditDonorsLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'content' => __('Edit'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isCurrentUserCan('edit_donors')) {
        return $link;
    }

    $args['atts']['href'] = getEditDonorsURL();

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'edit-link edit-donors-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getEditDonorLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'id' => 0,
        'content' => __('Edit'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isVaildID($args['id'])) {
        return $link;
    }

    if (! isCurrentUserCan('edit_donor')) {
        return $link;
    }

    $args['atts']['href'] = getEditDonorURL($args['id']);

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'edit-link edit-donor-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getDeleteDonorLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'id' => 0,
        'content' => __('Delete'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isVaildID($args['id'])) {
        return $link;
    }

    if (! isCurrentUserCan('delete_donor')) {
        return $link;
    }

    $args['atts']['href'] = getDeleteDonorURL($args['id']);

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'delete-link delete-donor-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getApproveDonorLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'id' => 0,
        'content' => __('Approve'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isVaildID($args['id'])) {
        return $link;
    }

    if (! isCurrentUserCan('approve_donor')) {
        return $link;
    }

    $donorRepository = EntityManager::getDonorRepository();
    $user = $donorRepository->find($args['id']);

    if ('pending' !== $user->get('status')) {
        return $link;
    }

    $args['atts']['href'] = getApproveDonorURL($args['id']);

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'approve-link approve-donor-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/*** Cities Template Tags *****************************************************/

/**
 * @return string
 * @since 1.0
 */
function getCitiesURL()
{
    $url = getSiteURL('/cities');
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getAddCityURL()
{
    $url = getSiteURL('/add/city');
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getEditCitiesURL()
{
    $url = getSiteURL('/edit/cities');
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getEditCityURL($id)
{
    $url = '';
    $id = (int) $id;

    if (! isVaildID($id)) {
        return $url;
    }

    $url = getSiteURL("/edit/city/{$id}");
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getDeleteCityURL($id)
{
    $url = '';
    $id = (int) $id;

    if (! isVaildID($id)) {
        return $url;
    }

    $url = addQueryArgs(getEditCitiesURL(), array(
        'action' => 'delete_city',
        'id' => $id,
    ));

    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getCitiesLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'content' => __('Cities'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isCurrentUserCan('view_cities')) {
        return $link;
    }

    $args['atts']['href'] = getCitiesURL();

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'view-link view-cities-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getAddCityLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'content' => __('Add'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isCurrentUserCan('add_city')) {
        return $link;
    }

    $args['atts']['href'] = getAddCityURL();

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'add-link add-city-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getEditCitiesLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'content' => __('Edit'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isCurrentUserCan('edit_cities')) {
        return $link;
    }

    $args['atts']['href'] = getEditCitiesURL();

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'edit-link edit-cities-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getEditCityLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'id' => 0,
        'content' => __('Edit'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isVaildID($args['id'])) {
        return $link;
    }

    if (! isCurrentUserCan('edit_city')) {
        return $link;
    }

    $args['atts']['href'] = getEditCityURL($args['id']);

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'edit-link edit-city-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getDeleteCityLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'id' => 0,
        'content' => __('Delete'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isVaildID($args['id'])) {
        return $link;
    }

    if (! isCurrentUserCan('delete_city')) {
        return $link;
    }

    $args['atts']['href'] = getDeleteCityURL($args['id']);

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'delete-link delete-city-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/*** Districts Template Tags ***************************************************/

/**
 * @return string
 * @since 1.0
 */
function getDistrictsURL()
{
    $url = getSiteURL('/districts');
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getAddDistrictURL()
{
    $url = getSiteURL('/add/district');
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getEditDistrictsURL()
{
    $url = getSiteURL('/edit/districts');
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getEditDistrictURL($id)
{
    $url = '';
    $id = (int) $id;

    if (! isVaildID($id)) {
        return $url;
    }

    $url = getSiteURL("/edit/district/{$id}");
    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getDeleteDistrictURL($id)
{
    $url = '';
    $id = (int) $id;

    if (! isVaildID($id)) {
        return $url;
    }

    $url = addQueryArgs(getEditDistrictsURL(), array(
        'action' => 'delete_district',
        'id' => $id,
    ));

    return $url;
}

/**
 * @return string
 * @since 1.0
 */
function getDistrictsLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'content' => __('Districts'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isCurrentUserCan('view_districts')) {
        return $link;
    }

    $args['atts']['href'] = getDistrictsURL();

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'view-link view-districts-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getAddDistrictLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'content' => __('Add'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isCurrentUserCan('add_district')) {
        return $link;
    }

    $args['atts']['href'] = getAddDistrictURL();

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'add-link add-district-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getEditDistrictsLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'content' => __('Edit'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isCurrentUserCan('edit_districts')) {
        return $link;
    }

    $args['atts']['href'] = getEditDistrictsURL();

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'edit-link edit-districts-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getEditDistrictLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'id' => 0,
        'content' => __('Edit'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isVaildID($args['id'])) {
        return $link;
    }

    if (! isCurrentUserCan('edit_district')) {
        return $link;
    }

    $args['atts']['href'] = getEditDistrictURL($args['id']);

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'edit-link edit-district-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/**
 * @return string
 * @since 1.0
 */
function getDeleteDistrictLink(array $args)
{
    $link = '';

    $args = array_merge(array(
        'id' => 0,
        'content' => __('Delete'),
        'atts' => array(),
        'before' => '',
        'after' => '',
    ), $args);

    if (! isVaildID($args['id'])) {
        return $link;
    }

    if (! isCurrentUserCan('delete_district')) {
        return $link;
    }

    $args['atts']['href'] = getDeleteDistrictURL($args['id']);

    if (! isset($args['atts']['class'])) {
        $args['atts']['class'] = 'delete-link delete-district-link';
    }

    $link = '<a' . get_html_atts($args['atts']) . '>' . $args['content'] . '</a>';
    return $args['before'] . $link . $args['after'];
}

/*** General Template Tags ****************************************************/

/**
 * @return void
 * @since 1.0
 */
function paginationLinks(array $args)
{
    $args = array_merge(array(
        'before' => '<nav class="pagination">',
        'after' => '</nav>',
        'type' => 'plain',
    ), $args);

	if ('array' === $args['type'] ) {
        $args['type'] = 'plain';
	}

    $links = getPaginationLinks($args);

    if (! empty($links)) {
        echo $args['before'] . $links . $args['after'];
    }
}

/**
 * @return string|array
 * @since 1.0
 */
function getPaginationLinks(array $args)
{
    $links = array();

    $args = array_merge(array(
        'total' => 1,
        'current' => 1,
        'base_url' => '',
        'page_url' => '',
        'type' => 'plain',
    ), $args);

	$args['total'] = (int) $args['total'];
    $args['total'] = max($args['total'], 1);

	if ($args['total'] > 1) {

        $args['current'] = (int) $args['current'];
        $args['current'] = max($args['current'], 1);

        for ($n = 1; $n <= $args['total']; $n++) {
            if ($n === $args['current']) {
                $links[] = '<span class="page-numbers current">' . number_format($n) . '</span>';
            } else {
                if (1 === $n) {
                    $url = $args['base_url'];
                } else {
                    $url = str_replace('%#%', $n, urldecode($args['page_url']));
                }
                $links[] = '<a class="page-numbers" href="' . esc_url($url) . '">' . number_format($n) . '</a>';
            }
        }

	}

    if ('plain' === $args['type']) {
        $links = implode("\n", $links);
    }

    return $links;
}
