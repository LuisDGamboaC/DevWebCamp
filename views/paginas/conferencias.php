<main class="agenda">
    <h2 class="agenda__heading"> WorkShops & Conferencias </h2>
    <p class="agenda__descripcion">Talleres y Conferencias dictados por expertos en desarrollo Web</p>

    <div class="eventos">
        <h3 class="eventos__heading">&lt;Conferencias/></h3>
        <p class="eventos__fecha"> Viernes 5 de octubre</p>

        <div class="eventos__listado slider swiper">
            <div class="swiper-wrapper">
            <?php foreach($eventos['conferencias_v'] as $evento ) { ?>
                <?php include __DIR__ . '../../templates/evento.php'; ?>
            <?php } ?>
            </div><!-- swiper--wrapper -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>

        <p class="eventos__fecha">Sábado 6 de octubre</p>

        <div class="eventos__listado slider swiper">
            <div class="swiper-wrapper">
            <?php foreach($eventos['conferencias_s'] as $evento ) { ?>
                <?php include __DIR__ . '../../templates/evento.php'; ?>
            <?php } ?>
            </div><!-- swiper--wrapper -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>

    <div class="eventos eventos--workshops">
        <h3 class="eventos__heading">&lt;WorkShops/></h3>
        <p class="eventos__fecha"> Viernes 5 de octubre</p>

        <div class="eventos__listado slider swiper">
            <div class="swiper-wrapper">
            <?php foreach($eventos['workshops_v'] as $evento ) { ?>
                <?php include __DIR__ . '../../templates/evento.php'; ?>
            <?php } ?>
            </div><!-- swiper--wrapper -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>

        <p class="eventos__fecha">Sábado 6 de octubre</p>
        
        <div class="eventos__listado slider swiper">
            <div class="swiper-wrapper">
                <?php foreach($eventos['workshops_s'] as $evento ) { ?>
                    <?php include __DIR__ . '../../templates/evento.php'; ?>
                <?php } ?>
            </div><!-- swiper--wrapper -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
</main>