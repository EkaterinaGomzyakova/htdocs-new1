<?
\Bitrix\Main\UI\Extension::load('wl.chosen');
?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('#main_tab_edit_table select').chosen();

        setInterval(() => {
            document.querySelectorAll('#productgrid select').forEach((item) => {
                item.setAttribute('size', 3);
            });
        }, 500);
    });
</script>