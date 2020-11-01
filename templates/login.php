<?php require_once('include/header.php') ?>
<div class="container">
    <div class="row">
        <div class="col-md-0 col-sm-0 col-lg-3">
        </div>
        <div class="col-md-0 col-sm-0 col-lg-6" style="padding: 5% 0">
            <form class="form-signin text-center" method="post" action="/login">
                <h1 class="h3 mb-3 font-weight-normal">TChat</h1>
                <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
                <br>
                <button class="btn btn-lg btn-primary btn-block" style="width: 30%;margin: auto" type="submit">Start
                    Chat
                </button>
            </form>
        </div>
        <div class="col-md- col-sm-0 col-lg-3">
        </div>
    </div>
</div>
<?php require_once('include/footer.php') ?>
