<?php
	include($_SERVER["DOCUMENT_ROOT"] . "/engine/kernel.php");
	$app = new Kernel();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title> Debaltsevo Web | Developer version </title>
		<?php 
			$app->showGeneralLayout();
			$app->showPageLayout("mainPage");
		?>
	</head>
	<body>
		<?php
			$app->ShowHeader();
		?>
		<div class="container">
			<div class="content">
				<?php
					$app->showMainPageBlocks();
				?>
				<div class="content__news-block main-page-block">
					<div class="news-block__container">
						<h1 class="news-block__container-title">
							Наши новости
						</h1>
						<div class="news-block__container-news">
							<?php
								$app->showMainPageNews();
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
			$app->ShowFooter();
		?>
	</body>
</html>
