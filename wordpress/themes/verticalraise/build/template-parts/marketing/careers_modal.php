<style>
    #resume-modal-upload form > p {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    #resume-modal-upload form > p:last-of-type {
        padding-top: 40px;
    }
    #resume-modal-upload .wpcf7-validation-errors {
        border-color: #ea5f5f !important;
        color: #ea5f5f !important;
        background: none !important;
        width: unset !important;
        margin: auto !important;
        border: none !important;
        font-family: "DKM-Night";
        font-size: 32px;
        font-weight: 400;
        float: none !important;
    }
    #resume-modal-upload .wpcf7-mail-sent-ok {
        color: white !important;
        border-color: #3CAB56 !important;
        background: none !important;
        width: unset !important;
        margin: auto !important;
        border: none !important;
        font-family: "DKM-Night";
        font-size: 32px;
        font-weight: 400;
        float: none !important;
    }
    #resume-modal-upload .wpcf7-submit {
        width: 230px;
        font: 400 36px "DKM-Night";
        color: #FFF;
        text-align: center;
        text-decoration: none;
        text-transform: uppercase;
        background-color: #52B6D5;
        height: 60px;
        line-height: 60px;
        border-radius: 5px;
        border: none;
    }
    #resume-modal-upload .ajax-loader {
        background-image: url(<?= get_template_directory_uri() . "/assets/images/ajax-loader.gif"; ?>) !important;
        width: 32px !important;
        height: 32px !important;
        position: relative !important;
        top: -46px !important;
        left: 80px !important;
    }
    #resume-modal-upload div.modal-body > p {
        padding-bottom: 25px;
        text-align: center;
    }
</style>

<div class="modal fade" id="resume-modal-upload" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                <h4 class="modal-title">Upload Resume</h4>
            </div>
            <div class="modal-body">
                <p>Please select file. Only are allowed PDF, ZIP, DOC, DOCX and TXT files. Max size is 5MB.</p>
                <?php
                $shortcode = get_field( "cf7_fileform_tag" );
                echo do_shortcode($shortcode);
                ?>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
