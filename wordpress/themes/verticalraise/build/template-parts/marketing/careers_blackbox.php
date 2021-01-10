<style>
    .blackbox_container {
        background: #231f20;
        padding-left: 5px;
        padding-right: 5px;
        padding-top: 100px;
        padding-bottom: 100px;
    }
    .blackbox_container .blackbox_section {
        max-width: 970px;
        margin: 0 auto;
        text-align: left;
    }
    .blackbox_container .blackbox_section .manager_header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .blackbox_container .blackbox_section .manager_header .manager_leftheader {
        display: flex;
        justify-content: space-around;
        align-items: center;
        width: 250px;
    }
    .blackbox_container .blackbox_section .manager_header .manager_leftheader img {
        max-height: 60px;
    }
    .blackbox_container .blackbox_section .manager_header .manager_leftheader span {
        color: white;
        font-size: 25px;
        text-transform: uppercase;
        font-family: "DKM-Night";
    }
    .blackbox_container .blackbox_section .manager_header .manager_leftheader:hover {
        cursor: pointer;
    }
    .blackbox_container .blackbox_section #resume_upload_btn:hover {
        border: solid 1px white;
    }
    .blackbox_container .blackbox_section h1 {
        color: white;
        font-weight: 400;
    }
    .blackbox_container .blackbox_section h3 {
        color: #4eb5d5;
        font-weight: 400;
        font-family: "Lato";
        letter-spacing: 0;
        font-size: 20px;
        margin-bottom: 20px;
    }
    .blackbox_container .blackbox_section p {
        font-family: "Lato";
        letter-spacing: 0;
        font-size: 20px;
        margin-bottom: 20px;
    }
    .blackbox_container .blackbox_section p:nth-of-type(1) {
        margin-bottom: 70px;
    }
    .blackbox_container .blackbox_section ul {
        list-style-position: inside;
        margin-left: 20px;
        margin-bottom: 20px;
    }
    .blackbox_container .blackbox_section ul li {
        font-size: 20px;
        font-weight: 300;
    }
    .blackbox_container .blackbox_section ol {
        list-style-position: inside;
        margin-bottom: 20px;
    }
    .blackbox_container .blackbox_section ol li {
        font-size: 20px;
        font-weight: 300;
        margin-bottom: 20px;
    }

    @media (max-width: 680px) {
        .blackbox_container {
            padding-top: 50px;
            padding-bottom: 50px;
        }
        .blackbox_container .blackbox_section p {
            font-size: 16px;
            font-weight: 300 !important;
        }
        .blackbox_container .blackbox_section h1 {
            font-size: 40px;
        }
        .blackbox_container .blackbox_section ul li {
            font-size: 16px;
        }
        .blackbox_container .blackbox_section ol li {
            font-size: 16px;
        }
    }

</style>


<div class="blackbox_container">
    <div class="blackbox_section">
        <!--CONTAINER start-->
        <div>

            <div class="manager_header">
                <h1 id="career_opportunities">Territory Sales Manager</h1>
                <div class="manager_leftheader" id="resume_upload_btn">
                    <img id="" src="<?php echo get_template_directory_uri() . "/assets/images/plus-icon.png"; ?>">
                    <span>Upload my resume</span>
                </div>
            </div>

            <p>Major US Markets</p>


            <h3>Job Description</h3>
            <p>A territory sales manager for Vertical Raise is a high-energy, goal driven, competitor with a
                passion to help colleges, high schools and community athletic and fine arts programs by selling
                and launching the industry’s most effective, custom, peer-to-peer, social media, crowd-funding
                campaigns designed to raise funds in a way that's safe, fun and 9 X's more effective than
                traditional fundraising. Our sales reps are the heart of our business; they receive
                protected territories, residual business and an incredible work / life balance.
            </p>

            <h3>Day-to-Day Responsibilities</h3>

            <ul>
                <li>Prospecting and setting meetings with Athletic Directors and coaches</li>
                <li>Presenting the value of the Vertical Raise platform</li>
                <li>Launching campaigns with teams and programs</li>
                <li>Coaching the coach for a successful campaign</li>
                <li>Working remotely from home office</li>
            </ul>


            <h3>Ideal Vertical Raise Candidate is/has</h3>

            <ul>
                <li>Sports - minded, competitor</li>
                <li>Excellent communicator</li>
                <li>Innovator who’s enthusiastic to grow their book of business</li>
                <li>Athletics or coaching experience (preferred)</li>
                <li>College degree (preferred)</li>
                <li>Sales experience (preferred)</li>
            </ul>

            <h3>A Vertical Rep is</h3>
            <ol>
                <li>
                    A bold conversation starter who knocks on doors, shakes hands, speaks with conviction,
                    listens well and networks their way to new clients and friendships.
                </li>
                <li>
                    The most competitive person they know; someone who wants to win their entire community to
                    safer, more effective fundraising.
                </li>
                <li>
                    Dependable. You know that one person you would call if you needed help and your phone was
                    about to die? Yeah, we want a whole team of those people!
                </li>
                <li>
                    Passionate for their community and loves to work with kids!
                </li>
                <li>
                    NOT a &#96;keyboard cowboy&#96;. Hates spreadsheets and loves being outside, like in the
                    sunshine with other humans.
                </li>
            </ol>


        </div>
        <!--CONTAINER end-->
    </div>
</div>
