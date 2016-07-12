$(document).ready(function() {

      $("#login").validate({
        rules: {
          email: {
            required: true,
            email: true
          },
          password: {
            required: true,
            minlength: 6
          }
        },

        submitHandler: function() {
          var data = $('#login').serialize();
          $.ajax({
            type: 'POST',
            url: 'submit/login.php',
            data: data,

            beforeSend: function() {
              $("#form-message").fadeOut('fast');
              $("#btn-submit").html(' Sending... ');
              $("#btn-submit").attr('disabled', true);
            },

            success: function(r) {
              if (r === 'success') {
                $("#btn-submit").html( r + ', Redirect... ');
                setTimeout(window.location.href = redirect_to, 5000);
              } else {
                  $("#form-message").fadeIn(0, function() {
                  $("#form-message").html('<div class="alert btn-danger"> ' + r + ' </div>');
                  $("#btn-submit").html(' Login');
                  $("#btn-submit").attr('disabled', false);
                });
              }
            }
          });
          return false;
        }

      });
});      