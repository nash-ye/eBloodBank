<?php
/**
 * The Footer
 *
 * @package EBloodBank
 * @subpackage Views
 * @since 1.0
 */
namespace EBloodBank\Views;

use EBloodBank as EBB;

?>

			<!-- Footer -->
			<footer>
				<div class="row">
					<div class="col-lg-12">
                        <p class="copyrights"><?php __e('Copyright &copy; eBloodBank 2015') ?></p>
					</div>
				</div>
			</footer>

		</div>
		<!-- /.container -->

        <script src="<?php echo EBB\escURL(EBB\getSiteURl('/public/jquery/jquery.min.js')) ?>"></script>
		<script src="<?php echo EBB\escURL(EBB\getSiteURl('/public/bootstrap/js/bootstrap.min.js')) ?>"></script>

	</body>

</html>
