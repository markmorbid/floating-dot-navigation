<?php
/**
 * Custom Control for Settings Management (Export/Import/Reset)
 */

if (!class_exists('WP_Customize_Control')) {
    return;
}

class Floating_DotNav_Settings_Control extends WP_Customize_Control {
    public $type = 'floating_dotnav_settings';
    
    public function render_content() {
        ?>
        <div class="floating-dotnav-settings-control" style="padding: 15px; background: #fff; border: 1px solid #ddd; border-radius: 4px; margin-top: 10px;">
            <label>
                <span class="customize-control-title" style="margin-bottom: 15px; display: block; font-weight: 600;">
                    <?php echo esc_html($this->label); ?>
                </span>
                <?php if (!empty($this->description)) : ?>
                    <span class="description customize-control-description" style="margin-bottom: 15px; display: block;">
                        <?php echo esc_html($this->description); ?>
                    </span>
                <?php endif; ?>
            </label>
            
            <div class="floating-dotnav-actions" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 15px;">
                <button type="button" class="button button-secondary floating-dotnav-export" id="floating-dotnav-export-btn">
                    <span class="dashicons dashicons-download" style="vertical-align: middle; margin-right: 5px;"></span>
                    <?php _e('Export Settings', 'floating-dotnav'); ?>
                </button>
                
                <button type="button" class="button button-secondary floating-dotnav-import" id="floating-dotnav-import-btn">
                    <span class="dashicons dashicons-upload" style="vertical-align: middle; margin-right: 5px;"></span>
                    <?php _e('Import Settings', 'floating-dotnav'); ?>
                </button>
                
                <button type="button" class="button button-secondary floating-dotnav-reset" id="floating-dotnav-reset-btn" style="color: #b32d2e;">
                    <span class="dashicons dashicons-update" style="vertical-align: middle; margin-right: 5px;"></span>
                    <?php _e('Reset to Defaults', 'floating-dotnav'); ?>
                </button>
            </div>
            
            <div class="floating-dotnav-import-wrapper" id="floating-dotnav-import-wrapper" style="display: none; margin-top: 10px;">
                <input type="file" accept=".txt" id="floating-dotnav-file-input" style="margin-bottom: 10px;">
                <p class="description" style="margin-top: 5px;">
                    <?php _e('Select a settings file to import.', 'floating-dotnav'); ?>
                </p>
            </div>
            
            <div class="floating-dotnav-notice-area" id="floating-dotnav-notice-area" style="margin-top: 10px;"></div>
        </div>
        <?php
    }
    
    public function enqueue() {
        // Enqueue will be handled by the main plugin class
    }
}

