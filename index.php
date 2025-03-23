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
			<div class="right-block">
				<div class="right-block__contacts">
					<h2 class="right-block__contact-title">
						Наши контакты:
					</h2>
					<div class="right-block__contact-content">
						<div class="right-block__contact-content-block">
							<span>Наша эл. почта: </span><a href="mailto:example@site.ru">example@site.ru</a>
						</div>
						<div class="right-block__contact-content-block">
							<span>Телефон 1: </span><a href="phone:89999999999">8 (999) 999 99-99</a>
						</div>
						<div class="right-block__contact-content-block">
							<span>Телефон 2: </span><a href="phone:81111111111">8 (111) 111 11-11</a>
						</div>
					</div>
				</div>
				<div class="right-block__menu">
					<h2 class="right-block__menu-title">
						Карта сайта
					</h2>
					<div class="rigth-block__menu-content">
						<div class="rigth-block__menu-content-block">
							<h3><a href="/">Главная сайта</a></h3>
						</div>
						<div class="rigth-block__menu-content-block">
							<h3><a href="#">Сведения об образовательной организации</a></h3>
							<h4><a href="#">Структура и органы управления образовательной организацией</a></h4>
							<h4><a href="#">Документы</a></h4>
							<h4><a href="#">Образование</a></h4>
							<h4><a href="#">Образовательные стандарты</a></h4>
							<h4><a href="#">Руководство. Педагогический состав</a></h4>
							<h4><a href="#">Стипендии и иные выплаты материальной поддержски</a></h4>
							<h4><a href="#">Материально-техническое обеспечение и оснащенность образовательного процесса</a></h4>
							<h4><a href="#">Финансово-хозяйственная деятельность</a></h4>
							<h4><a href="#">Вакантные места для приёма</a></h4>
							<h4><a href="#">Противодействие коррупции</a></h4>
						</div>

						<div class="rigth-block__menu-content-block">
							<h3><a href="#">Студентам</a></h3>
							<h4><a href="#">Комплексно-целевые программы</a></h4>
							<h4><a href="#">Стипендии и иные виды материальной поддержки</a></h4>
							<h4><a href="#">Общежитие</a></h4>
							<h4><a href="#">График учебного процесса</a></h4>
							<h4><a href="#">Дополнительное образование</a></h4>
							<h4><a href="#">WorldSkills</a></h4>
							<h4><a href="#">Фонд поддержки детей</a></h4>
						</div>

						<div class="rigth-block__menu-content-block">
							<h3><a href="#">Абитуриентам</a></h3>
							<h4><a href="#">Документы об организации работы приемной комиссии</a></h4>
							<h4><a href="#">Документы для поступления</a></h4>
							<h4><a href="#">Контрольные цифры приема студентов</a></h4>
							<h4><a href="#">Наши профессии</a></h4>
							<h4><a href="#">Вакантные места для приёма</a></h4>
							<h4><a href="#">Порядок приёма</a></h4>
							<h4><a href="#">Количество поданных заявлений</a></h4>
						</div>

						<div class="rigth-block__menu-content-block">
							<h3><a href="#">Выпускникам</a></h3>
							<h4><a href="#">Социальные партнеры</a></h4>
							<h4><a href="#">Советы психолога</a></h4>
							<h4><a href="#">Центр трудоустройства выпускников</a></h4>
						</div>

						<div class="rigth-block__menu-content-block">
							<h3><a href="#">Наша жизнь</a></h3>
							<h4><a href="#">Новости</a></h4>
							<h4><a href="#">Фотогалерея</a></h4>
							<h4><a href="#">Лучшие выпускники</a></h4>
						</div>

						<div class="rigth-block__menu-content-block">
							<h3><a href="#">Контакты</a></h3>
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
