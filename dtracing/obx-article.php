
<?php
/*
 * Template Name: OBX Article Template
 * Template Post Type: post
 */

get_header();
?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main seo-article">
				<?php while ( have_posts() ) : the_post(); ?>


					<div class="container standard-post-container">
						<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

							<div class="article-header">

								<div class="article-breadcrumbs">
									<ul itemscope itemtype="https://schema.org/BreadcrumbList">
										<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
											<a href="/" itemprop="item"><span itemprop="name">Home</span></a>
											<meta itemprop="position" content="1" />
										</li>
										<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
											<a href="/category/blog/" itemprop="item"><span itemprop="name">Articles</span></a>
											<meta itemprop="position" content="2" />
										</li>
										<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
											<span itemprop="item"><span itemprop="name"><?php the_title() ?></span></span>
											<meta itemprop="position" content="3" />
										</li>
									</ul>
								</div>
								<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

								<div class="article-meta-bar top-bar">
									<div class="author">By: Dusterhoff Racing</div>
									<div class="article-read"><span id="readTime"></span> min read time</div>
									<div class="sharer">
									<span class="share-title">Share:</span>
										<ul class="share-links"> 
											<li><a target="_blank" rel="noopener nofollow" href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink();?>"><span class="icon facebook-ic"></span></a></li>
											<li><a target="_blank" rel="noopener nofollow" href="https://x.com/intent/tweet?text=<?php the_title() ?>&url=<?php the_permalink();?>"><span class="icon instagram-ic"></span></a></li>
											<li><a target="_blank" rel="noopener nofollow" href="mailto:?subject=Check out this article from DTRacing.com!&amp;body=Check this out: <?php the_permalink();?>"><span class="icon email-ic"></span></a></li>
										</ul>
									</div>
								</div>

								<?php if ( has_post_thumbnail() ) : ?>
									<?php get_template_part( 'template-parts/partials/featured-image', get_post_type(), [ 'post_type' => get_post_type() ] ); ?>
								<?php endif; ?>
							</div>


							<div class="article-content">
								<?php the_content(); ?>
							</div><!-- .entry-content -->

							<div class="article-meta-bar bottom-bar">
								<div class="return-link"><a href="/category/blog/">All Articles</a></div>
								<div class="sharer">
								<span class="share-title">Share:</span>
								<ul class="share-links"> 
											<li><a target="_blank" rel="noopener nofollow" href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink();?>"><span class="icon facebook-ic"></span></a></li>
											<li><a target="_blank" rel="noopener nofollow" href="https://x.com/intent/tweet?text=<?php the_title() ?>&url=<?php the_permalink();?>"><span class="icon instagram-ic"></span></a></li>
											<li><a target="_blank" rel="noopener nofollow" href="mailto:?subject=Check out this article from DTRacing.com!&amp;body=Check this out: <?php the_permalink();?>"><span class="icon email-ic"></span></a></li>
										</ul>
								</div>
							</div>

							<?php
								$post_id = get_queried_object_id();
								$faq_section = get_field('faqs', $post_id);
								$content_wheel_section = get_field('content_wheel', $post_id);
								$cta_section = get_field('cta', $post_id);
							?>

							<?php if($faq_section){ ?> 
							<div class="obx article-template">
								<section class="faq" itemscope itemtype="https://schema.org/FAQPage">
									<h2 id="<?php echo $faq_section['anchor']; ?>" class="jump-target"><?php echo $faq_section['heading']; ?></h2>
									<?php if($faq_section['text']){ echo $faq_section['text']; } ?>
									<?php if(have_rows('faqs')): while(have_rows('faqs')): the_row(); ?>
										<?php if(have_rows('faq')): ?>
											<div class="faq-container">
											<?php while(have_rows('faq')): the_row(); 
											$question = get_sub_field('question');
											$answer = get_sub_field('answer');
											?>

											<div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
												<h3 class="accordion-head" itemprop="name"><?php echo $question ?></h3>
												<div class="accordion-content" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
													<div class="answer-wrapper" itemprop="text">
														<?php echo $answer ?>													
													</div>
												</div>
											</div>

									<?php endwhile; ?> </div> <?php endif; endwhile; endif; ?>
								</section>
							</div>
							<?php } ?>

						</article><!-- #post-## -->
					</div>

					<div class="full-width-article">
						<div class="obx article-template">
							<?php if($content_wheel_section){ ?> 
								<section class="content-wheel">
									<h2 id="<?php echo $content_wheel_section['anchor']; ?>" class="jump-target"><?php echo $content_wheel_section['heading']; ?></h2>
									<?php if($content_wheel_section['text']){ echo $content_wheel_section['text']; } ?>

									<?php if(have_rows('content_wheel')): while(have_rows('content_wheel')): the_row(); ?>
										<?php if(have_rows('related_article')): ?>
											<div class="related-articles">
											<?php while(have_rows('related_article')): the_row(); 
											$url = get_sub_field('url');
											$image = get_sub_field('image');
											$category = get_sub_field('category');
											$title = get_sub_field('title');
											$excerpt = get_sub_field('excerpt');
											?>
											<div class="article">
												<a class="image-link" href="<?php echo $url; ?>">
													<img height="157" width="367" src="<?php echo $image; ?>" alt="<?php echo $title; ?>" loading="lazy">
												</a>
												<span class="article-inner">
													<span class="category-badge"><?php echo $category; ?></span>
													<a href="<?php echo $url; ?>" class="article-title"><?php echo $title; ?></a>
													<span class="article-excerpt"><?php echo $excerpt; ?></span>
												</span>
											</div>

									<?php endwhile; ?> </div> <?php endif; endwhile; endif; ?>
								</section>
							<?php } ?>

							<?php if($cta_section){ ?> 
								<section class="cta">
									<div class="container standard-post-container">
										<h2 id="<?php echo $cta_section['anchor']; ?>" class="jump-target"><?php echo $cta_section['heading']; ?></h2>
										<?php if($cta_section['text']){ echo $cta_section['text']; } ?>
										<?php $link = $cta_section['button']; 
										if( $link ): 
										    $link_url = $link['url'];
											$link_title = $link['title'];
											$link_target = $link['target'] ? $link['target'] : '_self';
										?>
										<a class="obx-btn btn-purple" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
										<?php endif; ?>
									</div>
								</section>
							<?php } ?>
						</div>			
					</div>

				<?php endwhile; ?> <!-- End of the loop. -->
		</main><!-- #main -->
	</div><!-- #primary -->

	<!-- FAQ Accordion JS -->
	<script defer> 
		var acc = document.getElementsByClassName("accordion-head"); 
		var i;  
		
		if (acc.length > 0) {  
			acc[0].classList.add("active");  
			var firstContent = acc[0].nextElementSibling;  
			firstContent.style.maxHeight = firstContent.scrollHeight + "px"; 
		}  
		
		for (i = 0; i < acc.length; i++) {  
		acc[i].addEventListener("click", function () { 
			
			// Add logic to untoggle active class from all other items  
			for (var j = 0; j < acc.length; j++) {  
				if (acc[j] !== this) {  
					acc[j].classList.remove("active");  
					var otherContent = acc[j].nextElementSibling;  
					otherContent.style.maxHeight = null;  
				}  
			}  
			
			// Toggle active class for the clicked item  
			this.classList.toggle("active");  
			var content = this.nextElementSibling;  
			
			if (content.style.maxHeight) {  
				content.style.maxHeight = null;  
			} else {  
				content.style.maxHeight = content.scrollHeight + "px";  
			}  
			}); 
		} 
	</script>

	<!-- Article Read Time JS -->
	<script defer>
	document.addEventListener('DOMContentLoaded', function() {
		let articleText = document.getElementById('main').innerText;
		const wpm = 225;
		if(articleText){
			const words = articleText.trim().split(/\s+/).length;
			const time = Math.ceil(words / wpm);
			document.getElementById("readTime").innerText = time;
		}
	});
	</script>

	<style>
	#main.seo-article .container.standard-post-container{
		max-width: 733px;
		margin: 0 auto;
	}
	@media only screen and (max-width: 768px){
		#main.seo-article .container.standard-post-container{
			max-width: 100%;
			padding: 0 5%;
		}
	}
	#main.seo-article .article-header{
		margin: 30px 0 30px;
	}
	#main.seo-article .article-breadcrumbs{
		margin-bottom: 30px;
	}
	#main.seo-article .article-breadcrumbs ul {
		display: flex;
		align-items: center;
		list-style-type: none;
		margin: 0;
		padding: 0;
	}
	#main.seo-article .article-breadcrumbs ul li{
		color: #fff;
		padding: 0;
		margin: 0;
	}
	#main.seo-article .article-breadcrumbs ul li::before{
		content: '/';
		display: inline-block;
		margin: 0 5px 0 10px;
	}
	#main.seo-article .article-breadcrumbs ul li:first-child::before{
		display: none;
	}
	#main.seo-article .article-breadcrumbs ul li a{
		color: #fff;
		font-weight: bold;
		text-underline-offset: 3px;
	}
	#main.seo-article .article-breadcrumbs ul li a:hover{
		text-decoration: underline;
	}
	#main.seo-article h1{
		font-size: 42px;
		line-height: 50px;
		margin: 0 0 30px;
	}
	#main.seo-article .article-meta-bar{
		background-color: #1D1D1D;
		display: flex;
		justify-content: space-between;
		padding: 6px 10px;
		margin-bottom: 30px;
		font-size: 15px;
		line-height: 27px;
	}
	#main.seo-article .article-meta-bar.bottom-bar{
		margin: 60px 0;
	}
	#main.seo-article .article-meta-bar .sharer,
	#main.seo-article .share-links{
		display: flex;
		align-items: center;
		list-style-type: none;
		margin: 0;
		padding: 0;
	}
	#main.seo-article .share-links{
		column-gap: 5px;
	}
	#main.seo-article .share-links li a{
		background-color: #000;
		border-radius: 999px;
		padding: 5px;
		display: flex;
		justify-content: center;
		align-items: center;
		height: 24px;
		width: 24px;
		transition: 0.3s all;
	}
	#main.seo-article .share-links li a:hover{
		background-color: #2908F9;
	}
	#main.seo-article .share-title{
		margin-right: 10px;
	}
	#main.seo-article .article-meta-bar .return-link,
	#main.seo-article .article-meta-bar .return-link a{
		color: #fff;
		display: flex;
		align-items: center;
	}
	#main.seo-article .article-meta-bar .return-link a:hover{
		text-decoration: underline;
		text-underline-offset: 3px;
	}
	#main.seo-article .article-meta-bar .return-link a::before{
		background-image: url("data:image/svg+xml,%3C%3Fxml version='1.0' encoding='utf-8' %3F%3E%3Csvg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='10' height='9'%3E%3Cpath fill='white' transform='translate(0 0.0559082)' d='M8.8405256 4.4048758L5.6010838 7.8323936C5.3898716 8.0558691 5.0474286 8.0558691 4.836216 7.8323936C4.6250033 7.6089187 4.6250033 7.2465935 4.836216 7.0231185L7.1537662 4.5719671L0.53990686 4.5719671C0.24172455 4.5719671 0 4.3162084 0 4.0007143C0 3.6852198 0.24172455 3.429461 0.53990686 3.429461L7.1537662 3.429461L4.8371158 0.97688144C4.6259031 0.75340629 4.6259031 0.39108151 4.8371158 0.16760637C5.0483284 -0.055868782 5.3907714 -0.05586879 5.601984 0.16760635L8.8414249 3.5951245C8.9431019 3.7024572 9.0001688 3.8482316 9 4.0001979C8.9998312 4.152164 8.94244 4.2977962 8.8405256 4.4048758'/%3E%3C/svg%3E%0A");
		background-size: contain;
		background-position: center center;
		background-repeat: no-repeat;
		content: '';
		height: 12px;
		width: 14px;
		display: inline-block;
		margin-right: 5px;
		transform: rotate(180deg);
	}
	#main.seo-article .article-meta-bar .icon{
		display: inline-block;
		background-size: contain;
		background-position: center center;
		background-repeat: no-repeat;
		content: '';
	}
	#main.seo-article .article-meta-bar .icon.facebook-ic{
		background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512'%3E%3C!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--%3E%3Cpath fill='%23ffffff' d='M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z'/%3E%3C/svg%3E");
		height: 14px;
		width: 7.5px;
	}
	#main.seo-article .article-meta-bar .icon.instagram-ic{
		background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 448 512'%3E%3C!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--%3E%3Cpath fill='%23ffffff' d='M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z'/%3E%3C/svg%3E");
		height: 13px;
		width: 13px;
	}
	#main.seo-article .article-meta-bar .icon.email-ic{
		background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3C!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--%3E%3Cpath fill='%23ffffff' d='M64 112c-8.8 0-16 7.2-16 16l0 22.1L220.5 291.7c20.7 17 50.4 17 71.1 0L464 150.1l0-22.1c0-8.8-7.2-16-16-16L64 112zM48 212.2L48 384c0 8.8 7.2 16 16 16l384 0c8.8 0 16-7.2 16-16l0-171.8L322 328.8c-38.4 31.5-93.7 31.5-132 0L48 212.2zM0 128C0 92.7 28.7 64 64 64l384 0c35.3 0 64 28.7 64 64l0 256c0 35.3-28.7 64-64 64L64 448c-35.3 0-64-28.7-64-64L0 128z'/%3E%3C/svg%3E");
		height: 10px;
		width: 14px;
	}

	.obx.article-template h2,
	.obx.article-template .h2{
		display: block;
		font: 400 32px/1.188 var(--wp--preset--font-family--phonk);
		color: #fff;
		margin: 60px 0 20px;
	}   
	.obx.article-template h3,
	.obx.article-template .h3{
		display: block;
		font: 400 22px/1.273 var(--wp--preset--font-family--phonk);
		color: #fff;
		margin: 30px 0 20px;
	}
	.obx.article-template p,
	.obx.article-template li{
		font: 400 17px/1.588 var(--wp--preset--font-family--barlow);
		color: #fff;
		margin: 0 0 15px;
	}
	.obx.article-template ul{
		list-style-type: disc;
		padding-left: 30px;
	}
	.obx.article-template ul,
	.obx.article-template ol{
		margin-bottom: 15px;
	}
	.obx.article-template ul li{
		padding-left: 5px;
	}
	.obx.article-template ul li::marker{
		font-size: 20px;
		}
	.obx.article-template ul > :last-child,
	.obx.article-template ol > :last-child{
		margin-bottom: 0;
	}
	.obx.article-template a:not(.obx-btn){
		color: #fff;
		text-decoration: underline;
		text-underline-offset: 3px;
	}
	.obx.article-template a:not(.obx-btn):hover{
		text-decoration-thickness: 3px;
	}
	.obx.article-template p + .obx-btn{
		margin-top: 15px;
	}
	.obx.article-template .obx-btn{
		display: inline-flex;
		justify-content: center;
		align-items: center;
		padding: 20px 35px;
		font: 700 14px/1.588 var(--wp--preset--font-family--barlow);
		text-align: center;
		text-transform: uppercase;
		text-decoration: none !important;
		transition: 0.3s all;
	}
	.obx.article-template .obx-btn::after{
		background-image: url("data:image/svg+xml,%3C%3Fxml version='1.0' encoding='utf-8' %3F%3E%3Csvg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='10' height='9'%3E%3Cpath fill='white' transform='translate(0 0.0559082)' d='M8.8405256 4.4048758L5.6010838 7.8323936C5.3898716 8.0558691 5.0474286 8.0558691 4.836216 7.8323936C4.6250033 7.6089187 4.6250033 7.2465935 4.836216 7.0231185L7.1537662 4.5719671L0.53990686 4.5719671C0.24172455 4.5719671 0 4.3162084 0 4.0007143C0 3.6852198 0.24172455 3.429461 0.53990686 3.429461L7.1537662 3.429461L4.8371158 0.97688144C4.6259031 0.75340629 4.6259031 0.39108151 4.8371158 0.16760637C5.0483284 -0.055868782 5.3907714 -0.05586879 5.601984 0.16760635L8.8414249 3.5951245C8.9431019 3.7024572 9.0001688 3.8482316 9 4.0001979C8.9998312 4.152164 8.94244 4.2977962 8.8405256 4.4048758'/%3E%3C/svg%3E%0A");
		background-size: contain;
		background-position: center center;
		background-repeat: no-repeat;
		content: '';
		height: 12px;
		width: 14px;
		display: inline-block;
		margin-left: 10px;
	}
	.obx.article-template .obx-btn.btn-purple{
		background-color: #2908F9;
		color: #fff;
	}
	.obx.article-template .obx-btn.btn-purple:hover{ 
		background-color: #1e10c7;
	}
	.obx.article-template img{
		display: block;
		width: 100%;
	}
	.obx.article-template section:not(.content-wheel) img,
	.obx.article-template section:not(.product-callout) img{
		margin-top: 60px;
	}
	.obx.article-template .image-caption{
		padding: 15px 0;
		border-bottom: 1px solid #383838;
	}
	.obx.article-template section{
		margin: 60px 0; 
	}
	.obx.article-template .flex{
		display: flex;
	} 

	.obx.article-template .toc-head{
		display: block;
		font: 400 20px/1.9 var(--wp--preset--font-family--phonk);
		text-transform: uppercase;
		color: #fff;
		margin: 0 0 10px;
	}
	.obx.article-template .toc{
		background-color: #1D1D1D;
		padding: 30px;
	}
	.obx.article-template .toc ul{
		list-style-type: none;
		padding-left: 30px;
	}
	.obx.article-template .toc ul li{
		position: relative;
		margin: 0 0 10px;
	}
	.obx.article-template .toc ul li::before{
		position: absolute;
		background-image: url("data:image/svg+xml,%3C%3Fxml version='1.0' encoding='utf-8' %3F%3E%3Csvg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='14' height='13'%3E%3Cpath fill='white' transform='translate(0 0.0808105)' d='M12.782922 6.3692122L8.0988646 11.325218C7.7934628 11.648351 7.2983084 11.648351 6.9929066 11.325218C6.6875048 11.002085 6.6875048 10.478183 6.9929066 10.155049L10.343959 6.6108174L0.78067619 6.6108174C0.34952062 6.6108174 0 6.241004 0 5.7848163C0 5.3286285 0.34952062 4.9588151 0.78067619 4.9588151L10.343959 4.9588151L6.9942079 1.4125178C6.6888061 1.0893848 6.6888061 0.56548274 6.9942079 0.24234974C7.2996097 -0.08078324 7.794764 -0.080783248 8.1001654 0.24234971L12.784223 5.1983557C12.931242 5.3535528 13.013757 5.5643349 13.013514 5.78407C13.013268 6.0038052 12.930285 6.2143807 12.782922 6.3692122'/%3E%3C/svg%3E%0A");
		background-size: contain;
		background-position: center center;
		background-repeat: no-repeat;
		content: '';
		height: 13px;
		width: 14px;
		top: 9px;
		left: -29px;
	}
	.obx.article-template .toc a{
		text-decoration: none;
	}
	.obx.article-template .toc a:hover{
		text-decoration: underline;
		text-decoration-thickness: 1px;
	}
	.obx.article-template .jump-target{
		scroll-margin-top: 150px;
	}
	.obx.article-template .product-callout{
		padding: 60px 0;
		border-top: 1px solid #fff;
		border-bottom: 1px solid #fff;
	}
	.obx.article-template .product-callout .col{
		flex-basis: 50%;
		align-self: center;
	}
	.obx.article-template .product-callout .content-col{
		padding-left: 30px;
	}
	.obx.article-template .product-callout .product-title{
		display: block;
		font: 400 24px/1.167 var(--wp--preset--font-family--phonk);
		color: #fff;
		margin: 0 0 10px;
	}
	.obx.article-template .product-callout img{
		margin: 0 !important;
	}
	.obx.article-template .product-callout .product-price{
		font-size: 16px;
		line-height: 27px;
		margin: 0 0 20px;
	}
	.obx.article-template .product-callout .fine-print{
		display: block;
		font-size: 14px;
		line-height: 27px;
		margin-top: -5px;
	}
	.obx.article-template .mid-cta{
		background-color: #1D1D1D;
		padding: 30px;
	}
	.obx.article-template .video-container { 
		position: relative; 
		margin-top: 30px;
		padding-bottom: 56.25%; 
		height: 0; 
		overflow: hidden; 
	}
	.obx.article-template .video-container iframe,
	.obx.article-template .video-container object,
	.obx.article-template .video-container embed { 
		position: absolute; 
		top: 0; left: 0;
		width: 100%; height: 100%; 
	}
    
	.obx.article-template section.faq h2{
		font-size: 42px;
		line-height: 1.19;
		text-transform: uppercase;
	}
	.obx.article-template section.faq h3.accordion-head {
		font: 600 17px/1.588 var(--wp--preset--font-family--barlow);
        position: relative;
		cursor: pointer;
		margin: 0;
    }
	.obx.article-template section.faq .faq-item{
		padding: 20px 0;
		border-top: 1px solid #fff;
		padding-left: 50px;
	}
	.obx.article-template section.faq .faq-container{
		margin-top: 30px;
	}
	.obx.article-template section.faq .faq-container > .faq-item:last-child{
		border-bottom: 1px solid #fff;
	}
    .obx.article-template section.faq h3.accordion-head::before {
		background-image: url("data:image/svg+xml,%3C%3Fxml version='1.0' encoding='utf-8' %3F%3E%3Csvg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='22' height='21'%3E%3Cpath fill='white' transform='translate(0.205724 0)' d='M10 0C4.4861498 0 0 4.4861498 0 9.99998C0 11.63768 0.43134999 13.16838 1.13086 14.53318L0.046879999 18.41408C-0.20559999 19.315781 0.68595999 20.207279 1.58789 19.95508L5.4716802 18.87108C6.8353701 19.56918 8.3639002 19.999981 10 19.999981C15.5139 19.999981 20 15.51388 20 9.99998C20 4.4861498 15.5139 0 10 0ZM10 1.5C14.7031 1.5 18.5 5.2968602 18.5 9.99998C18.5 14.70318 14.7031 18.499981 10 18.499981C8.4991999 18.499981 7.0953002 18.109779 5.8710899 17.42778C5.6985302 17.331579 5.4949999 17.307381 5.3046899 17.36038L1.61133 18.390579L2.64258 14.69918C2.6958499 14.50858 2.6716299 14.30468 2.5752001 14.13188C1.89183 12.90678 1.5 11.50228 1.5 9.99998C1.5 5.2968602 5.2968502 1.5 10 1.5ZM5.75 7.5C5.65062 7.4986 5.55194 7.5169601 5.4597201 7.5540199C5.3674898 7.5910802 5.2835498 7.6461 5.21277 7.7158799C5.1420002 7.7856698 5.0857902 7.8688202 5.04743 7.9605098C5.0090699 8.0521803 4.9893198 8.1505804 4.9893198 8.24998C4.9893198 8.3493795 5.0090699 8.4477797 5.04743 8.5394802C5.0857902 8.6311798 5.1420002 8.7143803 5.21277 8.7840796C5.2835498 8.8538799 5.3674898 8.9088802 5.4597201 8.9459801C5.55194 8.9830799 5.65062 9.00138 5.75 8.99998L14.25 8.99998C14.3494 9.00138 14.4481 8.9830799 14.5403 8.9459801C14.6325 8.9088802 14.7164 8.8538799 14.7872 8.7840796C14.858 8.7143803 14.9142 8.6311798 14.9526 8.5394802C14.9909 8.4477797 15.0107 8.3493795 15.0107 8.24998C15.0107 8.1505804 14.9909 8.0521803 14.9526 7.9605098C14.9142 7.8688202 14.858 7.7856698 14.7872 7.7158799C14.7164 7.6461 14.6325 7.5910802 14.5403 7.5540199C14.4481 7.5169601 14.3494 7.4986 14.25 7.5L5.75 7.5ZM5.75 10.99998C5.65062 10.99858 5.55194 11.01698 5.4597201 11.05398C5.3674898 11.09108 5.2835498 11.14608 5.21277 11.21588C5.1420002 11.28568 5.0857902 11.36878 5.04743 11.46048C5.0090699 11.55218 4.9893198 11.65058 4.9893198 11.74998C4.9893198 11.84938 5.0090699 11.94778 5.04743 12.03948C5.0857902 12.13118 5.1420002 12.21438 5.21277 12.28408C5.2835498 12.35388 5.3674898 12.40888 5.4597201 12.44598C5.55194 12.48308 5.65062 12.50138 5.75 12.49998L12.25 12.49998C12.3494 12.50138 12.4481 12.48308 12.5403 12.44598C12.6325 12.40888 12.7164 12.35388 12.7872 12.28408C12.858 12.21438 12.9142 12.13118 12.9526 12.03948C12.9909 11.94778 13.0107 11.84938 13.0107 11.74998C13.0107 11.65058 12.9909 11.55218 12.9526 11.46048C12.9142 11.36878 12.858 11.28568 12.7872 11.21588C12.7164 11.14608 12.6325 11.09108 12.5403 11.05398C12.4481 11.01698 12.3494 10.99858 12.25 10.99998L5.75 10.99998Z'/%3E%3C/svg%3E%0A");
		background-size: contain;
		background-position: center center;
		background-repeat: no-repeat;
		content: '';
		position: absolute;
		height: 26px;
		width: 26px;
		left: -50px;
		top: 0;
    }
    .obx.article-template section.faq .accordion-content {
        max-height: 0;
        transition: 0.2s ease-out;
        overflow: hidden;
    }
	.obx.article-template section.faq .answer-wrapper{
		padding-top: 10px;
	}
    .obx.article-template section.faq .answer-wrapper > :last-child {
        margin-bottom: 0;
    }
	.obx.article-template .content-wheel{
		max-width: var(--wp--custom--wide-size);
		margin-left: auto;
		margin-right: auto;
	}
	.obx.article-template .content-wheel h2,
	.obx.article-template .content-wheel p{
		text-align: center;
	}
	.obx.article-template .related-articles{ 
		display: grid;
		margin-top: 50px;
		grid-template-columns: repeat(4, 1fr);
		column-gap: 30px;
		row-gap: 30px;
	}
	.obx.article-template .related-articles a,
	.obx.article-template .related-articles span{
		display: block;
	}
	.obx.article-template .related-articles img{
		margin-top: 0 !important;
		aspect-ratio: 324/139;
		width: 100%;
	}
	.obx.article-template .related-articles .article-inner{
		background-color: #1D1D1D;
		padding: 20px;
	}
	.obx.article-template .related-articles .category-badge{
		display: inline-block;
		font: 400 17px/1.588 var(--wp--preset--font-family--barlow);
		background-color: #2908F9;
		border-radius: 15px;
		color: #fff;
		padding: 2px 24px;
		text-align: center;
		margin: 0 0 15px;
	}
	.obx.article-template .related-articles .article-title{
		font: 700 20px/1.35 var(--wp--preset--font-family--barlow);
		text-transform: uppercase;
		text-decoration: none;
		margin: 0 0 10px;
		transition: 0.3s all;
	}
	.obx.article-template .related-articles .article-title:hover{
		color: #2908F9;
	}
	.obx.article-template .related-articles .article-excerpt{
		font: 400 17px/1.588 var(--wp--preset--font-family--barlow);
		color: #fff;
	}
	.obx.article-template .cta{
		background: #fff;
		padding: 60px 0;
		text-align: center;
	}
	.obx.article-template .cta .container > *:not(.obx-btn){
		text-align: center;
		color: #1D1D1D;
	}
	
	.obx.article-template > :first-child,
	.obx.article-template section > :first-child,
	.obx.article-template .image-caption > :first-child,
	.obx.article-template .col > :first-child,
	.obx.article-template .container > :first-child,
	.obx.article-template .inner-article > :first-child{
		margin-top: 0;
	}
	.obx.article-template > :last-child,
	.obx.article-template section > :last-child,
	.obx.article-template .image-caption > :last-child,
	.obx.article-template .col > :last-child,
	.obx.article-template .container > :last-child,
	.obx.article-template .inner-article > :last-child{
		margin-bottom: 0;
	}

	.obx.article-template section.content-wheel{
		margin-top: 60px !important;
	}

	@media only screen and (max-width: 768px){
		#main.seo-article .article-meta-bar.top-bar{
			flex-wrap: wrap;
		}
		#main.seo-article .article-meta-bar.top-bar .author{
			flex-basis: 50%;
			text-align: left;
			order: 1;
		}
		#main.seo-article .article-meta-bar.top-bar .article-read{
			flex-basis: 100%;
			text-align: left;
			order: 3;
		}
		#main.seo-article .article-meta-bar.top-bar .sharer{
			flex-basis: 50%;
			justify-content: flex-end;
			order: 2;
		}
		.obx.article-template section:not(.content-wheel) img,
		.obx.article-template section:not(.product-callout) img{
			margin-top: 40px;
		}
		.obx.article-template section{
			margin: 40px 0; 
		}
		.obx.article-template .flex{
			flex-direction: column;
		} 
		.obx.article-template .toc{
			padding: 20px 5%;
		}
		.obx.article-template .product-callout{
			padding: 40px 0;
		}
		.obx.article-template .product-callout .image-col{
			order: 1;
		}
		.obx.article-template .product-callout .content-col{
			order: 2;
			padding-top: 20px;
			padding-left: 0;
		}
		.obx.article-template .related-articles{ 
			margin-top: 30px;
			grid-template-columns: repeat(1, 1fr);
		}
		.obx.article-template .content-wheel{
			max-width: 100%;
			padding: 0 5%;
		}
		.obx.article-template .product-callout .col{
			align-self: unset;
		} 
	}
	</style>

<?php
get_footer();