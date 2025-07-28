<section class="bitanic__section bitanic__faq">
    <div class="bitanic__container">
        <div class="bitanic__section-header">
            <h1 class="bitanic__section-header-title">Pertanyaan yang sering ditanyakan</h1>
            <p class="bitanic__section-subtitle text-16 font-mulish font-medium">Lorem ipsum dolor sit amet, consectetur
                adipiscing elit. Vivamus hendrerit suscipit egestas. Nunc eget congue ante. Vivamus ut sapien et ex
                volutpat tincidunt eget at felis vivamus hendrerit.</p>
        </div>
        <div class="bitanic__faq-list">
            @for ($i = 0; $i < 6; $i++)
                <div class="bitanic__card">
                    <div class="bitanic__card-body">
                        <div class="bitanic__faq-card-body">
                            <div class="bitanic__faq-icon">
                                <img src="{{ asset('bitanic-landing/faq-1.png') }}" width="30" height="30" alt="">
                            </div>
                            <div>
                                <h5 class="bitanic__faq-card-title">What factors influence the cost of a landscaping
                                    project?</h5>
                                <p class="bitanic__faq-card-text">Vestibulum ligula sapien, cursus sed consectetur nec,
                                    tincidunt ac metus. Vivamus accumsan diam eget ultricies auctor.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</section>
