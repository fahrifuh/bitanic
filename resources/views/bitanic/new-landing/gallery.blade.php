<section class="bitanic__gallery">
    <div class="bitanic__container">
        <div class="bitanic__gallery-header font-bricolage">
            <h1 class="bitanic__gallery-title bitanic__text-secondary text-68">Galeri <span
                    class="bitanic__text-primary">Bitanic</span></h1>
        </div>
        <div class="bitanic__gallery-list">
            @for ($i = 0; $i < 6; $i++)
                <div class="bitanic__gallery-card">
                    <div class="bitanic__gallery-image">
                        <img src="{{ asset('bitanic-landing/gallery-1.png') }}" alt="gallery-1">
                    </div>
                    <div class="bitanic__gallery-overlay">
                        <div class="bitanic__gallery-card-body">
                            <h5 class="bitanic__gallery-card-title font-bricolage">Header</h5>
                            <p class="bitanic__gallery-card-text font-mulish">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Assumenda, est non blanditiis
                                accusamus rerum fugit perferendis esse suscipit itaque qui quis quaerat aliquid, culpa quae
                                velit eos dolore perspiciatis enim!</p>
                            <a href="#" class="bitanic__gallery-card-link font-mulish">Read more</a>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</section>
