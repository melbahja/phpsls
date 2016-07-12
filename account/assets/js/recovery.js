$(document).ready(function() {
      $("#recovery").validate({
        rules: {
          password: {
            required: true,
            minlength: 6
          },
          repassword: { 
            required: true,
            equalTo: '#password'
          }
        },
        messages: {
          repassword: {
            equalTo: 'the passwords did not match',
          }
        },
        submitHandler: function() {
          var data = $('#recovery').serialize();
          $.ajax({
            type: 'POST',
            url: 'submit/recovery.php',
            data: data,

            beforeSend: function() {
              $("#form-message").fadeOut('fast');
              $("#btn-submit").html(' Sending... ');
              $("#btn-submit").attr('disabled', true);
            },

            success: function(r) {
              if (r === 'success') {
                $("#btn-submit").attr('disabled', true);
                $("#btn-submit").html( r + ', Redirect... ');
                setTimeout(window.location.href = 'login.php', 5000);
              } else {
                  $("#form-message").fadeIn(0, function() {
                  $("#form-message").html('<div class="alert btn-danger"> ' + r + ' </div>');
                  $("#btn-submit").html(' Change password');
                  $("#btn-submit").attr('disabled', false);
                });
              }
            }
          });
          return false;
        }

      });
});      