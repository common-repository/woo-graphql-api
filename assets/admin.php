<div class="wrap">
  <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
  <nav class="nav-tab-wrapper">
    <a href="?page=wc-graphql" class="nav-tab <?php if ($tab===null):?>nav-tab-active<?php endif; ?>">General</a>
    <a href="?page=wc-graphql&tab=appSpecific" class="nav-tab <?php if ($tab==='appSpecific'):?>nav-tab-active<?php endif; ?>">App Specific</a>
    <a href="?page=wc-graphql&tab=mobileProvider" class="nav-tab <?php if ($tab==='mobileProvider'):?>nav-tab-active<?php endif; ?>">Mobile Provider Config</a>
    <a href="?page=wc-graphql&tab=menuConfig" class="nav-tab <?php if ($tab==='menuConfig'):?>nav-tab-active<?php endif; ?>">Menu Config</a>
  </nav>

  <div class="tab-content">
    <?php switch ($tab):
      case 'appSpecific':
        include plugin_dir_path(__DIR__) . 'assets/partials/appSpecificConfig.php';
        break;
      case 'mobileProvider':
        include plugin_dir_path(__DIR__) . 'assets/partials/mobileProviderForm.php';
        break;
      case 'menuConfig':
        include plugin_dir_path(__DIR__) . 'assets/partials/menuConfig.php';
        break;
      default:
        include plugin_dir_path(__DIR__) . 'assets/partials/generalConfig.php';
        break;
      endswitch; ?>
  </div>
</div>
