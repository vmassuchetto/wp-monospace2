		</div> <!-- #container -->
		<div class="bottom-navigation">
            <?php $params = monospace_scroll_params(); ?>
            <?php if (!isset($params['type']) || !$params['type']) : ?>
                <?php posts_nav_link(); ?>
            <?php endif; ?>
        </div>
        <div id="footer">
            <div class="infinite-scroll-wrap">
                <div class="infinite-scroll">
                    <span><?php _e('Loading more posts...', 'monospace2'); ?></span>
                </div>
            </div>
        </div>
        <?php wp_footer(); ?>
	</div> <!-- #wrap -->
</body>
</html>
