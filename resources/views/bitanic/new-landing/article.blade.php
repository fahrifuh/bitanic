<section class="bitanic__article">
    <div class="bitanic__container">
        <div class="bitanic__section-header">
            <h1 class="bitanic__section-title font-bricolage bitanic__text-secondary text-68 font-semibold">Artikel Baru
                <span class="bitanic__text-primary">Bitanic</span></h1>
            <p class="bitanic__section-subtitle text-16 font-mulish font-medium">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus
                hendrerit suscipit egestas. Nunc eget congue ante. Vivamus ut sapien et ex volutpat tincidunt eget at
                felis vivamus hendrerit.</p>
        </div>
        <div class="bitanic__article-list">
            @for ($i = 0;$i < 3;$i++)
                <div class="bitanic__article-card">
                    <div class="bitanic__article-card-image">
                        <img src="{{ asset('bitanic-landing/image-34.png') }}" alt="">
                    </div>
                    <div class="bitanic__article-card-body">
                        <h6 class="bitanic__article-card-subtitle">10 October 2024</h6>
                        <h5 class="bitanic__article-card-title">Creating a Pollinator Friendly Garden</h5>
                        <p class="bitanic__article-card-text">
                            Vivamus accumsan diam eget ultricies auctor. Proin iaculis metus vel condimentum tincidunt.
                        </p>
                        <a href="#" class="bitanic__article-card-link">Baca selanjutnya</a>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</section>
