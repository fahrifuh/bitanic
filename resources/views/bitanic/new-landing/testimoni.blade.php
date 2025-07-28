<section class="bitanic__testimonial">
    <div class="bitanic__container">
        <div class="bitanic__testimonial-header">
            <h1 class="bitanic__testimonial-title font-bricolage bitanic__text-secondary">Dengarkan
                <span class="bitanic__text-primary">Kata
                    Pengguna Kami</span>
            </h1>
            <p class="bitanic__testimonial-subtitle font-mulish text-16 font-medium">Lorem ipsum dolor sit amet
                consectetur, adipisicing elit. Quas incidunt
                placeat quia obcaecati, praesentium maxime dolores molestias, cum eius rerum, amet alias possimus neque
                nam hic fugit quisquam quidem reiciendis!</p>
        </div>
        <div class="bitanic__testimonial-list">
            @for ($i = 0;$i < 6;$i++)
                <div class="bitanic__testimonial-card">
                    <div class="bitanic__testimonial-card-body">
                        <div class="bitanic__testimonial-profile">
                            <div class="bitanic__testimonial-profile-image">
                                <img src="{{ asset('bitanic-landing/default-profile.png') }}" width="50" height="50" alt="profile-image">
                            </div>
                            <div class="bitanic__testimonial-profile-2">
                                <h3 class="bitanic__testimonial-profile-name">Charlie Leuschke</h3>
                                <div class="bitanic__testimonial-profile-ratings text-star">
                                    <i class='bx bxs-star'></i>
                                    <i class='bx bxs-star'></i>
                                    <i class='bx bxs-star'></i>
                                    <i class='bx bxs-star'></i>
                                    <i class='bx bxs-star'></i>
                                </div>
                            </div>
                        </div>
                        <p class="bitanic__testimonial-text">Phasellus fermentum orci non nunc fermentum mattis. In eleifend
                            vehicula justo, sed pulvinar erat scelerisque vel. Vestibulum eu erat elit. Etiam mattis feugiat
                            finibus.</p>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</section>
