<?php
/**
 * Template part for displaying employee settings
 */

// Security check
if (!defined('ABSPATH')) exit;

$current_user = wp_get_current_user();

// Get notification preferences
$notification_preferences = get_user_meta($current_user->ID, 'notification_preferences', true) ?: array();

// Default notification options if not set
$default_notifications = array(
    'application_status' => true,
    'interview_scheduled' => true,
    'job_recommendations' => true,
    'saved_job_expiring' => true
);

$notification_preferences = wp_parse_args($notification_preferences, $default_notifications);
?>

<div class="settings-container">
    <div class="row">
        <!-- Email Notifications -->
        <div class="col-lg-6 mb-4">
            <div class="card bg-dark border-secondary">
                <div class="card-header border-secondary">
                    <h3 class="h5 mb-0 text-light">
                        <i class="bi bi-bell me-2"></i>Email Notifications
                    </h3>
                </div>
                <div class="card-body">
                    <form id="notification-preferences-form" class="mb-0">
                        <?php wp_nonce_field('update_notification_preferences', 'notification_nonce'); ?>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="application_status" 
                                       name="notifications[application_status]" 
                                       <?php checked($notification_preferences['application_status']); ?>>
                                <label class="form-check-label text-light" for="application_status">
                                    Application Status Updates
                                </label>
                                <div class="text-muted small">Get notified when employers respond to your applications</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="interview_scheduled" 
                                       name="notifications[interview_scheduled]" 
                                       <?php checked($notification_preferences['interview_scheduled']); ?>>
                                <label class="form-check-label text-light" for="interview_scheduled">
                                    Interview Scheduled
                                </label>
                                <div class="text-muted small">Get notified when employers schedule interviews</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="job_recommendations" 
                                       name="notifications[job_recommendations]" 
                                       <?php checked($notification_preferences['job_recommendations']); ?>>
                                <label class="form-check-label text-light" for="job_recommendations">
                                    Job Recommendations
                                </label>
                                <div class="text-muted small">Receive personalized job recommendations based on your profile</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="saved_job_expiring" 
                                       name="notifications[saved_job_expiring]" 
                                       <?php checked($notification_preferences['saved_job_expiring']); ?>>
                                <label class="form-check-label text-light" for="saved_job_expiring">
                                    Saved Job Expiring
                                </label>
                                <div class="text-muted small">Get notified when saved jobs are about to expire</div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Save Notification Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Password Change -->
        <div class="col-lg-6 mb-4">
            <div class="card bg-dark border-secondary">
                <div class="card-header border-secondary">
                    <h3 class="h5 mb-0 text-light">
                        <i class="bi bi-shield-lock me-2"></i>Change Password
                    </h3>
                </div>
                <div class="card-body">
                    <form id="password-change-form" class="mb-0">
                        <?php wp_nonce_field('change_password', 'password_nonce'); ?>
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label text-light">Current Password</label>
                            <input type="password" class="form-control bg-dark text-light border-secondary" 
                                   id="current_password" name="current_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label text-light">New Password</label>
                            <input type="password" class="form-control bg-dark text-light border-secondary" 
                                   id="new_password" name="new_password" required 
                                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                                   title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters">
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label text-light">Confirm New Password</label>
                            <input type="password" class="form-control bg-dark text-light border-secondary" 
                                   id="confirm_password" name="confirm_password" required>
                        </div>

                        <div class="password-requirements text-muted small mb-3">
                            Password must contain:
                            <ul class="mb-0">
                                <li>At least 8 characters</li>
                                <li>At least one uppercase letter</li>
                                <li>At least one lowercase letter</li>
                                <li>At least one number</li>
                            </ul>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-shield-check me-2"></i>Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Handle notification preferences update
    $('#notification-preferences-form').submit(function(e) {
        e.preventDefault();
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        
        $.ajax({
            url: giggajob_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'update_notification_preferences',
                nonce: $('#notification_nonce').val(),
                preferences: $form.serialize()
            },
            beforeSend: function() {
                $submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Saving...'
                );
            },
            success: function(response) {
                if (response.success) {
                    alert('Notification preferences updated successfully!');
                } else {
                    alert(response.data.message || 'Failed to update preferences. Please try again.');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html(
                    '<i class="bi bi-save me-2"></i>Save Notification Settings'
                );
            }
        });
    });

    // Handle password change
    $('#password-change-form').submit(function(e) {
        e.preventDefault();
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        
        // Validate passwords match
        if ($('#new_password').val() !== $('#confirm_password').val()) {
            alert('New passwords do not match!');
            return;
        }
        
        $.ajax({
            url: giggajob_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'change_user_password',
                nonce: $('#password_nonce').val(),
                current_password: $('#current_password').val(),
                new_password: $('#new_password').val()
            },
            beforeSend: function() {
                $submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Updating...'
                );
            },
            success: function(response) {
                if (response.success) {
                    alert('Password updated successfully!');
                    $form[0].reset();
                } else {
                    alert(response.data.message || 'Failed to update password. Please try again.');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html(
                    '<i class="bi bi-shield-check me-2"></i>Update Password'
                );
            }
        });
    });

    // Password strength validation
    $('#new_password').on('input', function() {
        var password = $(this).val();
        var $requirements = $('.password-requirements li');
        
        $requirements.eq(0).toggleClass('text-success', password.length >= 8);
        $requirements.eq(1).toggleClass('text-success', /[A-Z]/.test(password));
        $requirements.eq(2).toggleClass('text-success', /[a-z]/.test(password));
        $requirements.eq(3).toggleClass('text-success', /\d/.test(password));
    });
});
</script> 