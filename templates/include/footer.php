<?php
@session_start();

use config\Parameters\Parameters;

$parameters = new Parameters();
?>
<!-- footer -->

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="assets/js/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>

<script>
    <?php use src\Model\DatabaseManager\DatabaseManager;
    $db = DatabaseManager::getInstance();
    $history = $db->getMessageHistory();
    ?>
    var wsUri = "<?php echo $parameters::SOCKET_HOST_JAVASCRIPT; ?>";
    websocket = new WebSocket(wsUri);

    function conversationScroll() {
    $('.conversation-wrap').scrollTop($('.conversation-wrap')[0].scrollHeight);
    }

    function addMessage(type, text) {

        var span = $('<span/>').addClass('msg-text').html(text);

        if (type == 'in') {
            var icon = $('<img/>').attr('src', 'assets/img/user.png').addClass("profile-left");
            $('<div/>').addClass('msg msg-in').appendTo('.conversation').append(icon).append(span);
        } else {
            var icon = $('<img/>').attr('src', 'assets/img/user.png').addClass("profile-left");
            $('<div/>').addClass('msg msg-out').appendTo('.conversation').append(span).append(icon);
        }
        conversationScroll();
    }

    window.addEventListener("load", function () {
        conversationScroll();
        $('.btn-send').click(function () {
            send();
        });

        var hs = <?php echo json_encode($history); ?>;
        hs.forEach(function (item, index) {

            if ("<?php echo $_SESSION['username'] ?>" == item["sender"]) {
                addMessage('out', item['msg'])
            } else
                addMessage('in', item['msg'])
        })


        websocket.onopen = function (ev) {
            // connection is open
            $('.status').html("<h3>Connected</h3>");


        }
    });

    websocket.onmessage = function (ev) {
        var data = JSON.parse(ev.data);
        var msg = data.message;
        if (msg !== undefined && msg != null && msg.length > 0) {
            addMessage('in',msg)
        }
    }

    function send() {
        if ($('#msg-input').val() != "") {
            var myMsg = $('#msg-input').val();
            var writeName = $('.write_name').val();
            var msg = {
                message: myMsg,
                sender: "<?php echo $_SESSION['username'] ?>",
            };
            console.log(msg)
            websocket.send(JSON.stringify(msg));
            addMessage('out', myMsg)
        }

    }

</script>
</body>
</html>
<!-- /footer -->