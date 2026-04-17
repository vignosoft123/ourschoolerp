
@layout('views/layouts/master')

@section('content')

    <!-- Breadcrumb Area -->
    <div class="bradcam-area area-padding">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="section-title white-title bradcam-title text-uppercase text-center">
                        <h2>Terms &amp; Conditions</h2>
                        <span class="star"></span>
                        <span class="star"></span>
                        <span class="star"></span>
                    </div>
                </div>
                <div class="bradcam-wrap text-center">
                    <nav class="bradcam-inner">
                        @if(customCompute($homepage))
                            <?php $hometype = (isset($homepage->pagesID) ? 'page' : (isset($homepage->postsID) ? 'post' : '')); ?>
                            <a class="bradcam-item text-uppercase" href="{{ base_url('frontend/'.$hometype.'/'.$homepage->url) }}">Home</a>
                        @else
                            <a class="bradcam-item text-uppercase" href="{{ base_url('frontend/index') }}">Home</a>
                        @endif
                        <span class="brd-separetor">/</span>
                        <span class="bradcam-item active text-uppercase">Terms &amp; Conditions</span>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumb Area -->

    <!-- Terms & Conditions Content -->
    <section class="terms-area area-padding">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="terms-content" style="background:#fff; padding:40px; border-radius:6px; line-height:1.9; color:#444;">

                        <h2 style="color:#333; margin-bottom:25px; font-size:26px; font-weight:700; text-transform:uppercase; border-bottom:3px solid #8dc63f; padding-bottom:12px;">
                            {{ $backend->sname }} &ndash; TERMS &amp; CONDITIONS
                        </h2>

                        <div style="margin-bottom:28px;">
                            <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">1. Acceptance of Terms</h4>
                            <p>By accessing or using the mobile application of <strong>{{ $backend->sname }}</strong>, users (students, parents, staff) agree to comply with these Terms &amp; Conditions. If you do not agree, please do not use the app.</p>
                        </div>

                        <div style="margin-bottom:28px;">
                            <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">2. User Eligibility</h4>
                            <p>This app is intended for students, parents/guardians, teachers, and authorized staff of the school. Login credentials will be provided by the school administration. Users must provide accurate and updated information.</p>
                        </div>

                        <div style="margin-bottom:28px;">
                            <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">3. Account Responsibility</h4>
                            <p>Users are responsible for maintaining the confidentiality of login credentials. Sharing login details with unauthorized persons is strictly prohibited. The school is not responsible for misuse of accounts.</p>
                        </div>

                        <div style="margin-bottom:28px;">
                            <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">4. App Usage</h4>
                            <p>Users agree to use the app only for viewing student details (attendance, marks, timetable), fee payments, school communication, homework and academic activities. Users must not misuse or attempt to hack the system, upload false or inappropriate content, or use the app for illegal purposes.</p>
                        </div>

                        <div style="margin-bottom:28px;">
                            <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">5. Fees &amp; Payments</h4>
                            <p>Fee payments made through the app are subject to payment gateway terms. The school is not responsible for delays or failures caused by third-party payment services. No refunds will be processed unless approved by the school management.</p>
                        </div>

                        <div style="margin-bottom:28px;">
                            <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">6. Data Privacy</h4>
                            <p>The school collects and uses data only for academic and administrative purposes. Personal data will be handled securely and not shared with unauthorized third parties. Users must not misuse other students' or staff information.</p>
                        </div>

                        <div style="margin-bottom:28px;">
                            <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">7. Notifications &amp; Communication</h4>
                            <p>The app may send notifications, alerts, and updates related to school activities. Users are responsible for checking updates regularly.</p>
                        </div>

                        <div style="margin-bottom:28px;">
                            <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">8. App Availability</h4>
                            <p>The school will try to ensure smooth functioning but does not guarantee uninterrupted access or error-free performance. Maintenance or technical issues may cause temporary downtime.</p>
                        </div>

                        <div style="margin-bottom:28px;">
                            <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">9. Intellectual Property</h4>
                            <p>All content (logos, data, design) belongs to <strong>{{ $backend->sname }}</strong>. Unauthorized copying or distribution is strictly prohibited.</p>
                        </div>

                        <div style="margin-bottom:28px;">
                            <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">10. Termination of Access</h4>
                            <p>The school reserves the right to suspend or terminate access if terms are violated or misuse is detected.</p>
                        </div>

                        <div style="margin-bottom:28px;">
                            <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">11. Limitation of Liability</h4>
                            <p>The school is not responsible for technical issues, data loss due to user negligence, or third-party service failures.</p>
                        </div>

                        <div style="margin-bottom:28px;">
                            <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">12. Changes to Terms</h4>
                            <p>The school may update these Terms &amp; Conditions at any time. Continued use of the app means acceptance of updated terms.</p>
                        </div>

                        <div style="margin-bottom:10px; background:#f9f9f9; border-left:4px solid #8dc63f; padding:20px 25px; border-radius:4px;">
                            <h4 style="color:#333; margin-bottom:14px; font-size:16px; font-weight:700;">13. Contact Information</h4>
                            <p style="margin-bottom:6px;">
                                <i class="fa fa-envelope-o" style="color:#8dc63f; margin-right:8px;"></i>
                                <strong>Email:</strong> {{ $backend->email }}
                            </p>
                            <p style="margin-bottom:6px;">
                                <i class="fa fa-phone" style="color:#8dc63f; margin-right:8px;"></i>
                                <strong>Phone:</strong> {{ $backend->phone }}
                            </p>
                            <p style="margin-bottom:0;">
                                <i class="fa fa-map-marker" style="color:#8dc63f; margin-right:8px;"></i>
                                <strong>Address:</strong> {{ $backend->address }}
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Terms & Conditions Content -->

@endsection
