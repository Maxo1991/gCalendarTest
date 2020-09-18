<?php
include 'template/header.php';
if(!empty($_POST['website'])) die();
?>
        <h1>Form</h1>
        <form action="quickstart1.php" method="POST">
            <div class="form-group row">
                <label for="example-name-input" class="col-2 col-form-label">Name</label>
                <div class="col-10">
                    <input class="form-control" type="text" placeholder="Name" id="example-name-input" name="example-name-input" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="example-tel-input" class="col-2 col-form-label">Phone</label>
                <div class="col-10">
                    <input class="form-control" type="tel" placeholder="0649944887" id="example-tel-input" name="example-tel-input" pattern="^\d{9,}$" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="example-email-input" class="col-2 col-form-label">Email</label>
                <div class="col-10">
                    <input class="form-control" type="email" placeholder="test@example.com" id="example-email-input" name="example-email-input" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="datepickerfrom" class="col-2 col-form-label">Date picker</label>
                <div class="col-10">
                    <input id="datepickerfrom" name="datepickerfrom" width="276" required />
                    <script>
                        $('#datepickerfrom').datepicker({
                            uiLibrary: 'bootstrap4'
                        });
                    </script>
                </div>
            </div>
            <div class="form-group row">
                <label for="timepickerstart" class="col-2 col-form-label">Start Event Time</label>
                <div class="col-10">
                    <input id="timepickerstart" name="timepickerstart" width="276" required/>
                    <script>
                        $('#timepickerstart').timepicker({
                            uiLibrary: 'bootstrap4'
                        });
                    </script>
                </div>
            </div>
            <div class="form-group row">
                <label for="timepickerend" class="col-2 col-form-label">End Event Time</label>
                <div class="col-10">
                    <input id="timepickerend" name="timepickerend" width="276" required/>
                    <script>
                        $('#timepickerend').timepicker({
                            uiLibrary: 'bootstrap4'
                        });
                    </script>
                </div>
            </div>
            <input type="text" id="website" name="website"/>
            <button type="submit" class="btn btn-primary" name="submit">Create Event</button>
        </form>
        <br>
<?php
include 'template/footer.php';