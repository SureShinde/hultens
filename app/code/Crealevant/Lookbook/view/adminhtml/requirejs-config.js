var config = {
	"map": {
		"*": {
			"lookbookUploader": "Crealevant_Lookbook/js/fileuploader",
			"lookbookAnnotate": "Crealevant_Lookbook/js/jquery.annotate",
		}
	},
	"paths": {            
		"lookbookUploader": "Crealevant_Lookbook/js/fileuploader",
		"lookbookAnnotate": "Crealevant_Lookbook/js/jquery.annotate",
	},   
    "shim": {
		"Crealevant_Lookbook/js/fileuploader": ["jquery"],
		"Crealevant_Lookbook/js/jquery.annotate": ["jquery"]
	}
};