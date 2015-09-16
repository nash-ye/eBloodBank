<?php
/**
 * View Donors Page
 *
 * @package EBloodBank
 * @subpackage Views
 * @since 1.0
 */
namespace EBloodBank\Views;

use EBloodBank as EBB;

View::display('header', ['title' => __('Donors')]);
?>

    <div class="btn-toolbar">
        <div class="btn-group" role="group">
            <?= EBB\getEditDonorsLink(['content' => __('Edit'), 'atts' => ['class' => 'btn btn-primary btn-edit btn-edit-donors']]) ?>
            <?= EBB\getAddDonorLink(['content' => __('Add New'), 'atts' => ['class' => 'btn btn-default btn-add btn-add-donor']]) ?>
        </div>
    </div>

    <?php View::display('notices') ?>

	<table id="table-donors" class="table table-bordered table-hover">

		<thead>
			<tr>
				<th>#</th>
				<th><?= EBB\escHTML(__('Name')) ?></th>
				<th><?= EBB\escHTML(__('Gender')) ?></th>
				<th><?= EBB\escHTML(__('Age')) ?></th>
				<th><?= EBB\escHTML(__('Blood Group')) ?></th>
                <th><?= EBB\escHTML(__('City')) ?></th>
				<th><?= EBB\escHTML(__('District')) ?></th>
				<th><?= EBB\escHTML(__('Phone Number')) ?></th>
			</tr>
		</thead>

		<tbody>

            <?php foreach ($view->get('donors') as $donor) : ?>

				<tr>
					<td><?php $donor->display('id') ?></td>
					<td><?php $donor->display('name') ?></td>
					<td><?= EBB\escHTML($donor->getGenderTitle()) ?></td>
					<td><?= EBB\escHTML($donor->calculateAge()) ?></td>
					<td><?php $donor->display('blood_group') ?></td>
                    <td><?php $donor->get('district')->get('city')->display('name') ?></td>
                    <td><?php $donor->get('district')->display('name') ?></td>
					<td><?= EBB\escHTML($donor->getMeta('phone')) ?></td>
				</tr>

            <?php endforeach; ?>

		</tbody>

	</table>

    <?=

        EBB\getPagination([
            'total'    => $view->get('pagination.total'),
            'current'  => $view->get('pagination.current'),
            'base_url' => EBB\getDonorsURL(),
            'page_url' => EBB\addQueryArgs(EBB\getDonorsURL(), ['page' => '%#%']),
        ])

    ?>

<?php
View::display('footer');
