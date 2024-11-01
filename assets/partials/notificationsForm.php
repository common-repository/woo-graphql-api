<?php
  $send_notification_nonce = wp_create_nonce('send_notification_form_nonce');
?>
<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
  <div class="container">
    <h3>For the full customization options <a href="https://reseller.dokku.shopz.io">Visit shopz dashboard</a> </h3> 
    <div>
      <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <input type="hidden" name="action" value="send_notification">
        <input type="hidden" name="send_notification_nonce" value="<?php echo $send_notification_nonce; ?>" />			

        <h3>Send Notification</h3>
        <div class="xs-col-12" style="margin-bottom: 10px;">
          <label for="title">Title</label>
          <textarea name="title" cols="30" rows="1" class="form-control" required style="resize: none;"></textarea>
        </div>
        <div class="xs-col-12">
          <label for="message">Message</label>
          <textarea name="message" cols="30" rows="10" class="form-control" required></textarea>
        </div>
        <div class="sm-col-12">
          <input type="submit" name="send_notification" value="Send" class="form-control btn btn-primary">
        </div>
      </form>
    </div>
  </div>
</form>
