$(document).ready(function() {

      $("#forgot").validate({
        rules: {
          email: {
            required: true,
            email: true
          }
        },

        submitHandler: function() {
          var data = $('#forgot').serialize();
          $.ajax({
            type: 'POST',
            url: 'submit/forgot.php',
            data: data,

            beforeSend: function() {
              $("#form-message").fadeOut('fast');
              $("#btn-submit").html(' Sending... ');
              $("#btn-submit").attr('disabled', true);
            },

            success: function(r) {
              if (r === 'success') {
                $("#btn-submit").remove();
                $("#forgotdata").remove();
                $("#form-message").html('<div class="alert btn-info"> please check your email </div>');
                $("#form-message").show();
              } else {
                  $("#form-message").fadeIn(0, function() {
                  $("#form-message").html('<div class="alert btn-danger"> ' + r + ' </div>');
                  $("#btn-submit").html(' Get new password');
                  $("#btn-submit").attr('disabled', false);
                });
              }
            }
          });
          return false;
        }

      });
});      
