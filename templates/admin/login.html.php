<?php
/** @var \App\Service\Router $router */
$title = 'Panel administracyjny';
$bodyClass = 'index';
ob_start(); ?>

    <div class="admin-grid">
    <div class="admin-card">
        <div class="login-form">
        <form onsubmit="event.preventDefault(); SendToAdmin();">
            <input type="password" id="accessCode" placeholder="Kod dostępu"/>
            <button type="submit" class="btn-primary">Zaloguj</button>
        </form>
    </div>
    </div>
    </div>
<script>
function SendToAdmin(){
    if (document.getElementById("accessCode").value==="Admin123!"){
        document.cookie="role=admin; path=/";
        window.location.href = "index.php?action=admin-index";
    } else{
        alert("Złe hasło");
    }

}
</script>
<?php $main = ob_get_clean();
include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
