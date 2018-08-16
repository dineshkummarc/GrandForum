DiversitySurveyView = Backbone.View.extend({

    initialize: function(){
        this.model.bind('sync', this.render, this);
        this.model.bind('change', this.change, this);
        this.template = _.template($('#diversity_template').html());
        _.defer(this.render);
    },
    
    events: {
    
    },
    
    change: function(initial){
        // Declining
        if(this.model.get('decline') == 1){
            if(initial === true){
                this.$("#reason").show();
                this.$("#survey").hide();
            }
            else{
                this.$("#reason").slideDown();
                this.$("#survey").slideUp();
            }
        }
        else{
            if(initial === true){
                this.$("#reason").hide();
                this.$("#survey").show();
            }
            else{
                this.$("#reason").slideUp();
                this.$("#survey").slideDown();
            }
        }
        
        // Birth Year
        if(this.model.get('birth') == "I prefer not to answer"){
            this.$("select[name=birth]").val("").prop("disabled", true);
        }
        else{
            this.$("select[name=birth]").prop("disabled", false);
        }
        
        // Indegenous
        if(this.model.get('indigenous') == "I prefer not to answer"){
            this.$("input[name=indigenous][type=radio]").prop("checked", false).prop("disabled", true);
        }
        else{
            this.$("input[name=indigenous][type=radio]").prop("disabled", false);
        }
        
        // Disability
        if(this.model.get('disability') == "I prefer not to answer"){
            this.$("input[name=disability][type=radio]").prop("checked", false).prop("disabled", true);
        }
        else{
            this.$("input[name=disability][type=radio]").prop("disabled", false);
        }
        
        // Disability Visibility
        if(this.model.get('disability') == "Yes"){
            if(initial === true){
                this.$("#disabilityVisibility").show();
            }
            else{
                this.$("#disabilityVisibility").slideDown();
            }
        }
        else{
            if(initial === true){
                this.$("#disabilityVisibility").hide();
            }
            else{
                this.$("#disabilityVisibility").slideUp();
            }
            this.$("input[name=disabilityVisibility]").prop("checked", false);
            this.model.set('disabilityVisibility', "");
        }
        if(this.model.get('disabilityVisibility') == "I prefer not to answer"){
            this.$("input[name=disabilityVisibility][type=radio]").prop("checked", false).prop("disabled", true);
        }
        else{
            this.$("input[name=disabilityVisibility][type=radio]").prop("disabled", false);
        }
        
        // Minority
        if(this.model.get('minority') == "I prefer not to answer"){
            this.$("input[name=minority][type=radio]").prop("checked", false).prop("disabled", true);
        }
        else{
            this.$("input[name=minority][type=radio]").prop("disabled", false);
        }
        
        // Race
        if(this.model.get('race').decline == "I prefer not to answer"){
            this.$("input[name=race_values][type=checkbox]").prop("checked", false).prop("disabled", true);
            this.$("input[name=race_other][type=text]").val("").prop("disabled", true);
            this.$("input[name=race_indigenousOther][type=text]").val("").prop("disabled", true);
            this.model.get('race').values = new Array();
            this.model.get('race').other = "";
            this.model.get('race').indigenousOther = "";
        }
        else{
            this.$("input[name=race_values][type=checkbox]").prop("disabled", false);
            this.$("input[name=race_other][type=text]").prop("disabled", false);
            this.$("input[name=race_indigenousOther][type=text]").prop("disabled", false);
        }
        
        // Racialized
        if(this.model.get('racialized') == "I prefer not to answer"){
            this.$("input[name=racialized][type=radio]").prop("checked", false).prop("disabled", true);
        }
        else{
            this.$("input[name=racialized][type=radio]").prop("disabled", false);
        }
        
        // Immigration
        if(this.model.get('immigration') == "I prefer not to answer"){
            this.$("input[name=immigration][type=radio]").prop("checked", false).prop("disabled", true);
            this.$("input[name=immigration][type=text]").val("").prop("disabled", true);
        }
        else{
            if(this.model.get('immigration') != "Canadian citizen" &&
               this.model.get('immigration') != "Permanent resident" &&
               this.model.get('immigration') != "Person from another country with a work or study permit"){
                this.$("input[name=immigration][type=radio]").prop("checked", false);
            }
            else{
                this.$("input[name=immigration][type=text]").val("");
            }
            this.$("input[name=immigration][type=radio]").prop("disabled", false);
            this.$("input[name=immigration][type=text]").prop("disabled", false);
        }
        
        // Gender
        if(this.model.get('gender').decline == "I prefer not to answer"){
            this.$("input[name=gender_values][type=checkbox]").prop("checked", false).prop("disabled", true);
            this.$("input[name=gender_other][type=text]").val("").prop("disabled", true);
            this.model.get('gender').values = new Array();
            this.model.get('gender').other = "";
        }
        else{
            this.$("input[name=gender_values][type=checkbox]").prop("disabled", false);
            this.$("input[name=gender_other][type=text]").prop("disabled", false);
        }
        
        // Sexuality
        if(this.model.get('sexuality').decline == "I prefer not to answer"){
            this.$("input[name=sexuality_values][type=checkbox]").prop("checked", false).prop("disabled", true);
            this.$("input[name=sexuality_other][type=text]").val("").prop("disabled", true);
            this.model.get('sexuality').values = new Array();
            this.model.get('sexuality').other = "";
        }
        else{
            this.$("input[name=sexuality_values][type=checkbox]").prop("disabled", false);
            this.$("input[name=sexuality_other][type=text]").prop("disabled", false);
        }
        console.log(this.model.toJSON());
    },
    
    render: function(){
        this.$el.html(this.template(this.model.toJSON()));
        this.change(true);
        return this.$el;
    }

});
