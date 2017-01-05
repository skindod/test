<?php if(session::get('notification')): ?>
<script type="text/javascript">
    $(document).ready(function () {
    var unique_id = $.gritter.add({
        // (string | mandatory) the heading of the notification
        title: 'Updated!',
        // (string | mandatory) the text inside the notification
        text: 'Your price have been updated." target="_blank" style="color:#ffd777">BlackTie.co</a>.',
        // (string | optional) the image to display on the left
        image: 'assets/img/ui-sam.jpg',
        // (bool | optional) if you want it to fade out on its own or just sit there
        sticky: true,
        // (int | optional) the time you want it to be alive for before fading out
        time: '',
        // (string | optional) the class name you want to apply to that specific message
        class_name: 'my-sticky-class'
    });

    return false;
    });
</script>
<?php session::remove('notification'); endif ?>

