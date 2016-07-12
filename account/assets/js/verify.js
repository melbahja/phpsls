$(document).ready(function() {
      $("#verify").validate({
        rules: {
          email: {
            required: true,
            email: true
          },
        },
        submitHandler: function() {
          var data = $('#verify').serialize();
         $.ajax({
            type: 'POST',
            url: 'submit/verify.php',
            data: data,

            beforeSend: function() {
              $("#form-message").fadeOut('fast');
              $("#btn-submit").html(' Sending... ');
              $("#btn-submit").attr('disabled', true);
            },

            success: function(r) {
              if (r === 'success') {
                $("#btn-submit").attr('disabled', true);
                $("email").remove();
                $(".form-signin").attr('align', 'center');
                $(".form-signin").html('<div class="btn btn-info">please check your email</div>');
              } else {
                  $("#form-message").fadeIn(0, function() {
                  $("#form-message").html('<div class="alert btn-danger"> ' + r + ' </div>');
                  $("#btn-submit").html(' Send verification link');
                  $("#btn-submit").attr('disabled', false);
                });
              }
            }
          });
          return false;
        }

      });
});      