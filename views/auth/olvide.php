<!-- BEM -->
<main class="auth">
    <h2 class="auth__heading"><?php echo $titulo; ?></h2>
    <!-- <a href="" class="auth__texto">Iniciar Sesión</a> -->
    <p class="auth__texto">Recupera tu acceso a DevWebCamp</p>

    <?php
        require_once __DIR__ . '/../templates/alertas.php';
    ?>

    <form method="POST" action="/olvide" class="formulario">
        <div class="formulario__campo">
            <label for="email" class="formulario__label">Email</label>
            <input type="email" class="formulario__input" placeholder="Tu Email" id="email" name="email">
        </div>

        <input type="submit" value="Enviar Instrucciones" class="formulario__submit">

        <div class="acciones">
            <a href="/olvide" class="acciones__enlace">Ya tienes Cuenta? Iniciar Sesión</a>
            <a href="/registro" class="acciones__enlace">¿Aún no tienes una cuenta? Obtener una</a>
        </div>
    </form>
</main>