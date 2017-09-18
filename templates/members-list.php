<div class="um-members-list">
    <div class="um-clear"></div>

    <?php foreach ( um_members( 'users_per_page' ) as $member ) {
        um_fetch_user( $member ); ?>

        <div class="um-member um-role-<?php echo um_user( 'role' ); ?> <?php echo um_user('account_status'); ?> <?php if ( $cover_photos ) { echo 'with-cover'; } ?>">
            <div class="um-clear"></div>

            <span class="um-member-status <?php echo um_user( 'account_status' ); ?>"><?php echo um_user( 'account_status_name' ); ?></span>

            <?php /*if ( $cover_photos ) {
                $sizes = um_get_option('cover_thumb_sizes');
                $cover_size = UM()->mobile()->isTablet() ? $sizes[1] : $sizes[0]; ?>

                <div class="um-member-cover" data-ratio="<?php echo um_get_option( 'profile_cover_ratio' ); ?>">
                    <div class="um-member-cover-e"><a href="<?php echo um_user_profile_url(); ?>" title="<?php echo esc_attr(um_user('display_name')); ?>"><?php echo um_user('cover_photo', $cover_size); ?></a></div>
                </div>
            <?php }*/ ?>

            <?php if ( $profile_photo ) {
                $default_size = str_replace( 'px', '', um_get_option( 'profile_photosize' ) );
                $corner = um_get_option( 'profile_photocorner' ); ?>

                <div class="um-member-photo radius-<?php echo $corner; ?>">
                    <a href="<?php echo um_user_profile_url(); ?>" title="<?php echo esc_attr( um_user( 'display_name' ) ); ?>">
                        <?php echo get_avatar( um_user('ID'), $default_size ); ?>
                    </a>
                </div>
            <?php } ?>

            <div class="um-member-card <?php echo ! $profile_photo ? 'no-photo' : '' ?>">

                <div class="um-member-card-actions">
                    <?php if ( UM()->roles()->um_current_user_can( 'edit', um_user('ID') ) || UM()->roles()->um_user_can( 'can_edit_everyone' ) ) { ?>
                        <div class="um-members-edit-btn">
                            <a href="<?php echo um_edit_profile_url() ?>" class="um-edit-profile-btn um-button um-alt">
                                <?php _e( 'Edit profile','ultimate-member' ) ?>
                            </a>
                        </div>
                    <?php }

                    do_action( 'um_members_just_after_name', um_user('ID'), $args ); ?>
                </div>

                <div class="um-member-card-info">
                    <div class="um-member-card-header">
                        <?php if ( $show_name ) { ?>
                            <div class="um-member-name">
                                <a href="<?php echo um_user_profile_url(); ?>" title="<?php echo esc_attr(um_user('display_name')); ?>">
                                    <?php echo um_user('display_name', 'html'); ?>
                                </a>
                            </div>
                        <?php }

                        do_action('um_members_after_user_name', um_user('ID'), $args); ?>
                    </div>

                    <div class="um-member-card-content">

                        <?php if ( $show_tagline && is_array( $tagline_fields ) ) {

                            um_fetch_user( $member );

                            foreach ( $tagline_fields as $key ) {
                                if ( $key && um_filtered_value( $key ) ) { ?>

                                    <div class="um-member-tagline um-member-tagline-<?php echo $key;?>"><?php echo um_filtered_value( $key ); ?></div>

                                <?php }
                            }
                        } ?>

                    </div>
                </div>
            </div>

            <?php if ( $show_userinfo ) { ?>

                <div class="um-member-meta-main">

                    <?php if ( $userinfo_animate ) { ?>
                        <div class="um-member-more"><a href="#"><i class="um-faicon-angle-down"></i></a></div>
                    <?php } ?>

                    <div class="um-member-meta <?php echo ! $userinfo_animate ? 'no-animate' : '' ?>">

                        <?php foreach ( $reveal_fields as $key ) {
                            if ( $key && um_filtered_value( $key ) ) { ?>

                                <div class="um-member-metaline um-member-metaline-<?php echo $key; ?>">
                                    <span>
                                        <strong><?php echo UM()->fields()->get_label( $key ); ?>:&nbsp;</strong> <?php echo um_filtered_value( $key ) ?>
                                    </span>
                                </div>

                            <?php }
                        }

                        if ( $show_social ) { ?>
                            <div class="um-member-connect"><?php UM()->fields()->show_social_urls(); ?></div>
                        <?php } ?>

                    </div>

                    <div class="um-member-less"><a href="#"><i class="um-faicon-angle-up"></i></a></div>

                </div>

            <?php } ?>

            <div class="um-clear"></div>
        </div>

        <?php um_reset_user_clean();
    }

    um_reset_user(); ?>

    <div class="um-clear"></div>
</div>