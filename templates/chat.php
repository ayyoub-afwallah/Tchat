<?php require_once('include/header.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-0 col-sm-0 col-lg-3">
            </div>
            <div class="col-md-12 col-sm-12 col-lg-6">
                <div class="chat">
                    <div class="card">
                        <div class="card-header text-center">
                            TChat : <?php
                            echo isset($_SESSION['username']) ? $_SESSION['username'] : ''
                            ?>
                        </div>
                        <div class="conversation-wrap">
                            <div class=" conversation ">

                                <div class="msg-in msg">
                                    <img src="assets/img/user.png " class="profile">
                                    <span class="msg-text"><span class="sender-name">name</span>Cras justo od justo od justo </span>
                                </div>

                                <div class="msg-out msg">
                                    <span class="msg-text">Cras justo odio</span>
                                    <img src="assets/img/user.png " class="profile">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="controls">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control " id="msg-input">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary btn-send" type="button">Send</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-0 col-sm-0 col-lg-3">

            </div>
        </div>
    </div>

<?php require_once('include/footer.php') ?>