<style type="text/css">
    body {
        font-size: 10px;
        background-color: #565cc0;
        /* Malibu color */
        font-family: "Open Sans", sans-serif;
        display: grid;
        height: 100vh;
        place-items: center;
        overflow: hidden;
    }

    .c-checkbox {
        display: none;
    }

    .c-checkbox:checked~.c-form .c-form__eyeIcon::before {
        transform: scale(20);
    }

    .c-checkbox:checked~.c-form .c-form__eyeIconBar::before {
        transform: scaleX(0);
    }

    .c-checkbox:checked~.c-form .c-form__lockIcon {
        color: #121726;
        /* Mirage color */
    }

    .c-form {
        position: relative;
        overflow: hidden;
        width: 41em;
        height: 8em;
        padding: 2em 3.125em;
        box-sizing: border-box;
        border-radius: 1.25em;
        background-color: #121726;
        /* Mirage color */
        box-shadow: 0 0.125em 0.125em 0 rgba(0, 0, 0, 0.14),
            0 0.1875em 0.0625em -0.125em rgba(0, 0, 0, 0.12),
            0 0.0625em 0.3125em 0 rgba(0, 0, 0, 0.2);
        display: flex;
    }

    .c-form__input {
        flex-grow: 1;
        font-size: 1.5625em;
        font-family: inherit;
        color: #eaea;
        /* Indigo color */
        border: 0;
        outline: 0;
        padding: 0 0.88em;
        box-sizing: border-box;
        background-color: transparent;
        z-index: 2;
    }

    .c-form__input::placeholder {
        color: #cccccc;
        /* Silver color */
    }

    .c-form__eyeIcon {
        position: relative;
        width: 4em;
        height: 4em;
        border-radius: 50%;
        color: #575dbe;
        /* Blue-Violet color */
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .c-form__eyeIcon::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        border-radius: inherit;
        background-color: #ffffff;
        /* White color */
        transition: 0.15s;
        pointer-events: none;
    }

    .c-form__eyeIcon::after {
        content: "";
        height: 0.875em;
        width: 0.875em;
        border-radius: 50%;
        background-color: currentColor;
        box-shadow: 0 0 0 0.375em #ffffff, 0 0 0 0.625em;
        z-index: 1;
    }

    .c-form__eyeIconBar {
        position: absolute;
        width: 2.8125em;
        height: 0.25em;
        transform: rotate(45deg);
        z-index: 2;
    }

    .c-form__eyeIconBar::before {
        content: "";
        display: block;
        width: inherit;
        height: inherit;
        background-color: currentColor;
        transform-origin: bottom right;
        transform: scaleY(1);
        transition: transform 0.15s;
    }

    .c-form__lockIcon {
        order: -1;
        position: relative;
        width: 4em;
        height: 4em;
        color: #ffffff;
        /* White color */
        z-index: 1;
        transition: 0s 0.1s;
    }

    .c-form__lockIcon::before,
    .c-form__lockIcon::after {
        content: "";
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }

    .c-form__lockIcon::before {
        bottom: 0.875em;
        width: 1.75em;
        height: 1.25em;
        border-radius: 0.3125em 0.3125em 0 0;
        background-color: currentColor;
    }

    .c-form__lockIcon::after {
        bottom: 2em;
        /* Adjusted for visual consistency */
        width: 1.5em;
        height: 1.125em;
        border: 0.25em solid;
        border-bottom: 0;
        box-sizing: border-box;
        border-radius: 1.5em 1.5em 0 0;
    }

    /* Additional style for the label */
    .c-form__label {
        font-size: 3.2em;
        color: #ffffff;
        font-weight: bold;
    }

    /* Full-screen overlay with a semi-transparent background */
    #overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        /* Semi-transparent black background */
        display: flex;
        /* Use flexbox to center the spinner */
        justify-content: center;
        /* Center horizontally */
        align-items: center;
        /* Center vertically */
        display: none;
        /* Hidden by default */
        z-index: 9999;
        /* Ensures overlay is on top */
    }

    @keyframes  spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<div id="overlay">
</div>
<input type="checkbox" class="c-checkbox" id="unmask">
<form class="c-form" action="<?php echo e(url('tax/login')); ?>" method="POST" id="form-login">
    <?php echo csrf_field(); ?>
    <input class="c-form__input js-inputField" type="password" placeholder="PIN" name="password" autofocus>
    <span class="c-form__lockIcon" class=""></span>
    <label class="c-form__eyeIcon js-unmask" for="unmask">
        <b class="c-form__eyeIconBar"></b>
    </label>
</form>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).ajaxStart(function() {
            $('#overlay').fadeIn(); // Show the overlay
        });

        $(document).ajaxStop(function() {
            $('#overlay').fadeOut(); // Hide the overlay
        });
        document
            .getElementsByClassName("js-unmask")[0]
            .addEventListener("click", function() {
                const field = this.parentNode.querySelector(".js-inputField");
                field.type = field.type == "password" ? "text" : "password";
            });

        $('.js-inputField').on('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });


        $(document).on('keypress', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                $.ajax({
                    data: $('#form-login').serialize(),
                    url: "<?php echo e(url('tax/login')); ?>",
                    type: "POST",
                    dataType: 'json',
                    success: function(res) {
                        if (res.status == 'error') {
                            $('body').css('background-color', 'red');
                            $('.js-inputField').val('');
                            alert('Incorrect PIN..');
                        } else {
                            $('body').css('background-color', '');
                            location.href = "<?php echo e(url('tax/index')); ?>";
                            sessionStorage.setItem("token", res.token);
                        }
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            }
        });
    });
</script>
<?php /**PATH C:\#PROJECT#\#WEBAPP\mkt_psi\resources\views/new/tax/home.blade.php ENDPATH**/ ?>