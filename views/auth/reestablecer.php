<!-- BEM -->
<main class="auth">
    <h2 class="auth__heading"><?php echo $titulo; ?></h2>
    <!-- <a href="" class="auth__texto">Iniciar Sesión</a> -->
    <p class="auth__texto">Coloca tu nuevo Password</p>

    <?php
        require_once __DIR__ . '/../templates/alertas.php';
    ?>

    <?php if($token_valido) { ?>

    <form method="POST" class="formulario">
        <div class="formulario__campo">
            <label for="password" class="formulario__label">Nuevo Password</label>
            <input type="password" class="formulario__input" placeholder="Tu Password" id="password" name="password">
        </div>

        <input type="submit" value="Guardar Password" class="formulario__submit">
    </form>
    <?php } ?>
        <div class="acciones">
            <a href="/olvide" class="acciones__enlace">Ya tienes Cuenta? Iniciar Sesión</a>
            <a href="/registro" class="acciones__enlace">¿Aún no tienes una cuenta? Obtener una</a>
        </div>

</main>