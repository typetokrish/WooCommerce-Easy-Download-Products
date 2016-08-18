<h2 class="tk_ed_heading">{heading}</h2>
<p class="tk_ed_description">{description}</p>
<div class="form-group tk_ed_error" id="tk_ed_frm_error"></div>
<div class="col-lg-12" id="tk_ed_dwnform">
	<div class="col-lg-12">
    <div class="form-group">
        <label for="name">{label_name}<span class="tk_ed_req">*</span></label>
        <input type="text" name="tk_ed_name" id="tk_ed_name" class="form-control tk_ed_input" placeholder="{place_name}" />
    </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group">
            <label for="name">{label_email}<span class="tk_ed_req">*</span></label>
            <input type="text" name="tk_ed_email" id="tk_ed_email" class="form-control tk_ed_input" placeholder="{place_email}" />
            <input type="hidden" name="tk_ed_hidden" id="tk_ed_hidden" value="{tk_ed_hidden}">
        </div>
    </div>
    <div class="col-lg-12">
        <p>{info_text}</p>
    </div>
	<div class="col-lg-12">
    <div class="form-group">    
       <p> <input type="button" name="tk_ed_button" class="button button-normal" id="tk_ed_button" value="{place_button}" onClick="tk_ed_validate_and_save()" /></p>
    </div>
   </div>
</div>
<div class="col-lg-12" id="tk_ed_dwnsummary"></div>