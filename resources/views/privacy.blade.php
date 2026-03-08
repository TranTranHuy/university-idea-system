@extends('layouts.master') {{-- Thay 'layouts.app' bằng tên file layout chính của bạn --}}

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-md-10">

            <div class="text-center mb-5">
                <h1 class="fw-bold text-dark display-4 mb-3">Privacy Policy</h1>
                <p class="text-muted">Effective Date: March 2026</p>
            </div>

            <div class="content-body text-secondary" style="font-size: 1.05rem; line-height: 1.8;">
                <p>Thank you for using the University Idea Management System (UIMS). Your privacy and data security are extremely important to the University. This Privacy Policy explains how the system collects, uses, stores, and protects personal information when staff members access the platform, submit ideas, or participate in discussions.</p>

<p>The University is committed to ensuring that all personal data collected through this system is handled responsibly and in accordance with internal data protection policies. By accessing or using the UIMS platform, you acknowledge that your information may be collected and processed for operational and administrative purposes related to quality assurance and institutional improvement.</p>

<h4 class="fw-bold text-dark mt-5 mb-3">1. Information We Collect</h4>
<p>When you use the University Idea Management System, certain information may be collected in order to maintain the functionality, security, and administrative oversight of the platform. The types of information collected include:</p>

<ul>
<li class="mb-2"><strong>Account Data:</strong> Information associated with your university account such as your full name, official university email address, department affiliation, and system role (e.g., Staff Member, QA Coordinator, QA Manager, or Administrator).</li>

<li class="mb-2"><strong>User Content:</strong> Any ideas you submit to the platform, comments you post on existing ideas, feedback through the rating system (Thumbs Up or Thumbs Down), and any supporting files or documents that you choose to upload.</li>

<li class="mb-2"><strong>System Activity Data:</strong> Technical information generated through system usage, including timestamps of submissions, system interaction logs, and activity records used for monitoring system performance and maintaining security.</li>
</ul>

<p>This information allows the system administrators to ensure that the platform functions properly and that all interactions remain consistent with university guidelines.</p>

<h4 class="fw-bold text-dark mt-5 mb-3">2. How We Use Your Information</h4>
<p>The data collected through the University Idea Management System is used exclusively for internal university purposes. The primary objectives of collecting this information include improving institutional processes and facilitating the idea management workflow.</p>

<p>Your information may be used to:</p>

<ul>
<li class="mb-2">Send automated notifications to the appropriate Department QA Coordinator whenever a new idea is submitted within their department.</li>

<li class="mb-2">Notify the original author when other users comment on their submitted ideas or participate in discussions related to their proposals.</li>

<li class="mb-2">Generate analytical reports and statistical summaries such as the number of ideas submitted by each department, the percentage distribution of ideas, and the number of active contributors across the university.</li>

<li class="mb-2">Monitor the overall effectiveness of the idea management process and support decision-making within the University's quality assurance framework.</li>
</ul>

<p>The University does not use this data for commercial purposes and will not sell or distribute personal information to external third parties.</p>

<h4 class="fw-bold text-dark mt-5 mb-3">3. Anonymous Submissions</h4>
<p>The University recognizes that some staff members may feel more comfortable sharing suggestions anonymously. Therefore, the UIMS platform allows users to submit ideas or comments without displaying their identity publicly.</p>

<p>If you choose to submit content anonymously, your name will not be visible to other staff members on the platform. However, in order to maintain accountability and protect the integrity of the system, your user identification information will still be securely stored within the system database.</p>

<p>This internal record enables authorized administrators to investigate potential misuse of the platform, such as inappropriate content, harassment, or violations of the University's terms of use.</p>

<p><strong>Anonymous posting protects your identity from other users, but it does not remove administrative accountability within the system.</strong></p>

<h4 class="fw-bold text-dark mt-5 mb-3">4. Data Export and Retention</h4>
<p>At the end of each academic year or after the final closure date of an idea submission cycle, the University Quality Assurance Manager may export system data for reporting and evaluation purposes.</p>

<p>This exported data may include idea submissions, comments, ratings, and participation statistics. The data may be downloaded in structured formats such as CSV files, while uploaded supporting documents may be archived and downloaded in ZIP format.</p>

<p>The exported information is used for internal analysis, reporting, and institutional improvement planning. Data retention policies follow the University's internal data management guidelines and are designed to ensure responsible handling of staff contributions.</p>

<h4 class="fw-bold text-dark mt-5 mb-3">5. Data Security</h4>
<p>The University implements role-based access control within the system to ensure that sensitive data is protected and only accessible to authorized personnel. Access to system information is restricted based on user roles and responsibilities.</p>

<p>For example, QA Coordinators may access idea submissions within their departments, while QA Managers and system administrators may access broader analytical reports and system monitoring tools.</p>

<p>Technical safeguards, access restrictions, and system monitoring procedures are used to protect the platform against unauthorized access, misuse, or data manipulation.</p>

<p>The University continuously reviews and improves its security practices to ensure that personal information and system data remain protected.</p>
                <hr class="my-5">
                <p class="text-center small text-muted">By continuing to use the UIMS, you acknowledge that you have read and understood this Privacy Policy.</p>
            </div>

        </div>
    </div>
</div>
@endsection
