    	   </section>
        </div>

        <footer>
            <?php
            wp_nav_menu(array(
                'theme_location' => 'footer',
                'container'      => 'nav'
            ));
            ?>
        </footer>

    	<?php wp_footer() ?>

    	<script>
            window.ga=function(){ga.q.push(arguments)};ga.q=[];ga.l=+new Date;
            ga('create','UA-XXXXX-Y','auto');ga('send','pageview')
        </script>
        <script src="https://www.google-analytics.com/analytics.js" async defer></script>

    </body>
</html>
