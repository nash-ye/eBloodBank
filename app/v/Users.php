<?php

namespace eBloodBank;

/**
 * @since 0.3
 */
class Users_View extends Default_View {

	/**
	 * @return string
	 * @since 0.3
	 */
	public function get_title() {
		return 'المستخدمين';
	}

	/**
	 * @return void
	 * @since 0.3
	 */
	public function __invoke() {

		$can_add    = current_user_can( 'add_user' );
		$can_edit   = current_user_can( 'edit_user' );
		$can_delete = current_user_can( 'delete_user' );
		$can_manage = current_user_can( 'manage_users' );

		$this->template_header(); ?>

			<?php if ( $can_add ) : ?>
			<a href="<?php site_url( array( 'page' => 'add-user' ) ) ?>" class="add-link">
				<button type="button">أضف مستخدم جديد</button>
			</a>
			<?php endif; ?>

			<table id="table-users" class="list-table">

				<thead>
					<th>#</th>
					<th>الاسم</th>
					<th>الرتبة</th>
					<?php if ( $can_manage ) : ?>
					<th>الإجراءات</th>
					<?php endif; ?>
				</thead>

				<tbody>

					<?php

						$users = Users::fetch_all();

						if ( ! empty( $users ) && is_array( $users ) ) {

							foreach( $users as $user ) {

								?>

								<tr>
									<td><?php $user->display( 'user_id' ) ?></td>
									<td><?php $user->display( 'user_logon' ) ?></td>
									<td>
										<?php
											$user_role = $user->get_role();
											echo ( $user_role ) ? $user_role->title : $user->get( 'user_role' );
										?>
									</td>
									<?php if ( $can_manage ) : ?>
									<td>
										<?php if ( $can_edit ) : ?>
										<a href="<?php site_url( array( 'page' => 'edit-user', 'id' => $user->get( 'user_id' ) ) ) ?>" class="edit-link"><i class="fa fa-pencil"></i></a>
										<?php endif; ?>
										<?php if ( $can_delete && $user->get_ID() !== Sessions::get_current_user_ID() ) : ?>
										<a href="<?php site_url( array( 'page' => 'users', 'action' => 'delete_user', 'id' => $user->get( 'user_id' ) ) ) ?>" class="delete-link"><i class="fa fa-trash"></i></a>
										<?php endif; ?>
									</td>
									<?php endif; ?>
								</tr>

								<?php

							}

						}

					?>

				</tbody>

			</table><?php

		$this->template_footer();

	}

}