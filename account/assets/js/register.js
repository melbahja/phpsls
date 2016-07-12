$(document).ready(function() {
  
      $("#register").validate({
        rules: {
          fname: {
            required: true,
            minlength: 3,
            maxlength: 19
          },
          lname: {
            required: true,
            minlength: 3,
            maxlength: 19
          },
          username: {
            required: true,
            minlength: 4,
            maxlength: 15
          },
          email: {
            required: true,
            email: true
          },
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
            equalTo: 'The Passwords did not match'
          }
        },
        submitHandler: function() {
          var data = $('#register').serialize();
          $.ajax({
            type: 'POST',
            url: 'submit/register.php',
            data: data,

            beforeSend: function() {
              $("#form-message").fadeOut('fast');
              $("#btn-submit").html(' Sending... ');
              $("#btn-submit").attr('disabled', true);
            },

            success: function(r) {
              if (r === 'success') {
                $("#btn-submit").html( r + ', Redirect... ');
                setTimeout(window.location.href = 'verify.php', 5000);
              } else {
                  $("#form-message").fadeIn(0, function() {
                  $("#form-message").html('<div class="alert btn-danger"> ' + r + ' </div>');
                  $("#btn-submit").html(' Register');
                  $("#btn-submit").attr('disabled', false);
                });
              }
            }
          });
          return false;
        }

      });
});      