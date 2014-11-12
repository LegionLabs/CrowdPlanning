<!DOCTYPE html>

<header>

    <link   rel="stylesheet" type="text/css" href="./css/css.css">
    <link   rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
    <script src="libs/jquery-2.1.0.min.js" type="text/javascript"></script>
    <script src="libs/gup.js" type="text/javascript"></script>
    <script type="text/javascript" src="./js/script.js"></script>

</header>

<body>

<div class="container-custom">
    <div class="container-center">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-xs-12">

            <div class="col-lg-6 col-sm-6 col-xs-6 border overall-margin">

                <div class="inner-container">
                </div>
                <div class="bottom-text">
                    <p class="mrg">Current Plan</p>
                </div>
            </div>

            <div class="col-lg-6 col-sm-6 col-xs-6 border overall-margin">
                <div class="inner-container">

                </div>
                <div class="bottom-text">
                    <p class="mrg">Suggested Plan</p>
                </div>
            </div>

        </div>
    </div>
        <div class="row">
        <div class="col-lg-12 col-sm-12 col-xs-12">

            <div class="col-lg-6 col-sm-6 col-xs-6 border overall-margin">
                <div class="inner-container" id="action_library">

                </div>
                <div class="bottom-text">
                    <p class="mrg">Action/ role library</p>
                </div>
            </div>

            <div class="col-lg-6 col-sm-6 col-xs-6 border overall-margin">
                <div class="inner-container">

                </div>
                <div class="bottom-text">

                    <input type="text" placeholder="Enter action and press enter" id="input_add" class="form-control" width="100%" />

                    <p class="mrg">

                        <input type="button" value="Add" id="add_new" class="btn btn-success"/>
                        <input type="button" id="suggest_new" value="Suggest new action" class="btn btn-success"/>

                    </p>
                </div>
            </div>
        </div>
        </div>
</div>

</body>
</html>