<div class="alert alert-success" id="success" role="alert">
    <?php
    foreach ($_REQUEST['success'] as $success) {
        echo '<p>' . htmlspecialchars($success) . '</p>';
    }
    ?>
</div>

<script>
    var card = document.getElementById('success');
    if (card !== null) {
        setTimeout(() => {
            card.remove();
        }, 2500);
    }
</script>