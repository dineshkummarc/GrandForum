SopsRowView = Backbone.View.extend({
    
    tagName: 'tr',
    parent: null,
    template: _.template($('#sops_row_template').html()),
    additionalNotesDialog: null,
    renderedOnce: false,
    
    initialize: function(options){
        this.parent = options.parent;
        this.listenTo(this.model, "sync", this.render);
    },

    events: {
        'click #additionalNotes': 'addNotes',
    },

    addNotes: function() {
        if (this.additionalNotesDialog == null) {
            var additionalNotesDialogId = 'additionalNotes_' + this.model.attributes['id'];
            var model = this.model;
            var view = this;
            var previousAdditional;
            this.additionalNotesDialog = $("#" + additionalNotesDialogId);
            this.additionalNotesDialog.dialog({
                autoOpen: false,
                resizable: false,
                //height: 400,
                open: function(event, ui) {
                    previousAdditional = _.clone(model.get('additional'));
                },
                closeOnEscape: true,
                buttons: {
                    "Save": function() {
                        model.save();
                        $(this).dialog("close");
                        view.$('#notes .throbber').show();
                    },
                    "Cancel": function() {
                        $(this).dialog("close");
                    }
                },
                close: function() {
                    model.set('additional', previousAdditional);
                    $('textarea', $(this)).val(previousAdditional.notes[me.get('lastname').replace(/[^\wA-zÀ-ÿ]/gi, '')]);
                },
                show: { effect: "drop", direction: "down", duration: 250 }
            });
            this.additionalNotesDialog.parent().appendTo(this.$("#notes"));
        }

        //var additionalNotesDialogId = 'additionalNotes_' + this.model.attributes['id'];

        //$('#my-dialog').parent().css({position:"fixed"}).end().dialog('open');
        this.additionalNotesDialog.parent().css({position:"fixed"}).end().dialog('open');
        //$("#additionalNotes_" + this.model.attributes['id']).dialog('open');
    },

    render: function(){
        var i = this.model.toJSON();
        var mod = _.extend(this.model.toJSON());
        this.el.innerHTML = this.template(mod);
        for(m=0;m<i.annotations.length;m++){
            if(i.annotations[m].tags != null){
                for(n=0;n<i.annotations[m].tags.length;n++){
                    var comment_column = "#span"+i.sop_id;
                    if(m == i.annotations.length-1){
                        $(comment_column).append(i.annotations[m].tags[n]);
                        break;
                    }
                    $(comment_column).append(i.annotations[m].tags[n]+", ");
                }
            }
        }
        this.additionalNotesDialog = null;
        if(this.renderedOnce){
            this.parent.renderRoles();
        }
        this.renderedOnce = true;
        return this.$el;
    }
});