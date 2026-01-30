<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - OKS Fast Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css folder/about us.css">
</head>

<body>

    <?php include 'header.php'; ?>

    <section class="about-hero">
        <div class="hero-content">
            <h1>TWP 4213</h1>
            <h2>Internet and Web Publishing</h2>
        </div>
    </section>

    <section class="team-section">
        <div class="container">
            <h3 class="section-heading">Group OKS - Fastfood ordering system</h3>
            <div class="row g-4 justify-content-center">


                <div class="col-md-6 col-lg-3">
                    <div class="team-card">
                        <div class="member-img-box">
                            <img src="image/Ong Wen Bin.jpeg" onerror="this.src='https://via.placeholder.com/150?text=Member'"
                                class="member-img" alt="Ong Wen Bin">
                        </div>
                        <h4 class="member-name">Ong Wen Bin</h4>
                        <span class="member-id">243DT243BX</span>
                        <p class="member-role">Member</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="team-card">
                        <div class="member-img-box">
                            <img src="image/Law Jie Po.jpeg" onerror="this.src='https://via.placeholder.com/150?text=Member'"
                                class="member-img" alt="Law Jie Po">
                        </div>
                        <h4 class="member-name">Law Jie Po</h4>
                        <span class="member-id">243DT245TU</span>
                        <p class="member-role">Team Leader</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="team-card">
                        <div class="member-img-box">
                            <img src="image/Wong Yew Jonn.jpeg" onerror="this.src='https://via.placeholder.com/150?text=Member'"
                                class="member-img" alt="Wong Yew Jonn">
                        </div>
                        <h4 class="member-name">Wong Yew Jonn</h4>
                        <span class="member-id">243DT34373</span>
                        <p class="member-role">Member</p>
                    </div>
                </div>

            </div>

        </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>