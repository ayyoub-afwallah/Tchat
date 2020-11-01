<?php
@session_start();

use config\Parameters\Parameters;

$parameters = new Parameters();
?>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>

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

    function addMessage(type, text, sender = null) {

        var span = $('<span/>').addClass('msg-text').html(text);
        var senderName = $('<span/>').addClass('sender-name').html(sender);

        if (type == 'in') {
            var icon = $('<img/>').attr('src', 'assets/img/user.png').addClass("profile");
            span.prepend(senderName);
            $('<div/>').addClass('msg msg-in').appendTo('.conversation').append(icon).append(span);
        } else {
            var icon = $('<img/>').attr('src', 'assets/img/user.png').addClass("profile");
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
                addMessage('out', item['msg'], item['sender'] + " : ")
            } else
                addMessage('in', item['msg'], item['sender'] + " : ")

        })

        websocket.onopen = function (ev) {
            // connection is open
            $('.card-header').css('color', 'green')
        }
    });

    websocket.onmessage = function (ev) {
        var data = JSON.parse(ev.data);
        var msg = data.message;
        if (msg !== undefined && msg != null && msg.length > 0) {
            addMessage('in', msg, data.sender + " : ")
        }
    }

    function send() {
        if ($('#msg-input').val() != "") {

            var myMsg = $('#msg-input').val();
            var msg = {
                message: myMsg,
                sender: "<?php echo $_SESSION['username']?$_SESSION['username']:'' ?>",
            };
            websocket.send(JSON.stringify(msg));
            addMessage('out', myMsg)
        }

    }

</script>
</body>
</html>
