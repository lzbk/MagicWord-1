var combo = {
	newIds: [],
	previousIds: [],
	currentComboLength: 0,
	currentComboLemmas: [],
	comboSelector: $("#combo"),

	handleNewIds: function(callback){
		var increment = false;
		var comboEnded = false;
		if (this.currentComboLemmas.length === 0) {
			//ajout des nouveaux lemmes dans currentcombos
			for (var i = this.newIds.length; i--;) {
				this.currentComboLemmas.push(this.newIds[i]);
			}
			this.currentComboLength = 1;
			increment = true;
		} else {
			// suppression dans current combos des lemmes qui ne font pas partie des nouveaux lemmes.
			for (var i = this.currentComboLemmas.length; i--;) {
				if ($.inArray(this.currentComboLemmas[i],this.newIds) == -1) {
					this.currentComboLemmas.splice(i,1);
				} else if (!increment) {
					increment = true;
					this.currentComboLength++;
				}
			}
		}

		if(!increment){
			comboEnded = true;
			this.endCombo(this.currentComboLength);
			this.currentComboLength = 0;
			//ptete un nouveau combo est commencé
			for (var i = this.previousIds.length; i--;) {
				if ($.inArray(this.previousIds[i],this.newIds) != -1) {
					increment = true;
					this.currentComboLength = 2;
				}
			}
		}

		if (comboEnded === false) {
			// faudrait prendre en compte le possible incrément précédent avant de tout effacer.
			callback();
		}

		// ajout des nouveaux lemmes dans currentcombos
		for (var i = this.newIds.length; i--;) {
			if ($.inArray(this.newIds[i], this.currentComboLemmas) == -1) {
				this.currentComboLemmas.push(this.newIds[i]);
				if (!increment) {
					this.currentComboLength = 1;
				}
			}
		}

		this.sendPoints();
		this.showCombo();
	},

	handleNewInflection: function(inflection){
		this.previousIds = this.newIds;
		this.newIds = gridJSON.inflections[inflection.toLowerCase()].lemmaIds;
		this.handleNewIds(objectiveCombo.checkLastObjective);

		return;
	},

	showCombo: function(){
		$(".current-combo").removeClass("reached");

		if (this.currentComboLength > 1) {
			for (var i = 1; i <= this.currentComboLength - 1; i++) {
				$("#current-combo-"+i).addClass("reached");
			}
		} else if (this.currentComboLength == 1) {
			$(".current-combo").removeClass("locked");
		} else {
			$(".current-combo").addClass("locked");
		}

	},

	reset: function(){
		this.endCombo(this.currentComboLength);
		this.newIds = [];
		this.previousIds = [];
		this.currentComboLength = 0;
		this.currentComboLemmas = [];
		this.showCombo();
	},

	endCombo: function(length){
		if (length > 1) {
			activity.sendComboFinished(length);
			objectiveCombo.checkObjectives(length, false);
		}
		this.showCombo();
	},

	sendPoints: function(){
		if (this.currentComboLength > 1 && roundJSON.type == "rush") {
			activity.sendComboPoints(this.currentComboLength);
		}
	},

	displayPoints: function(points, length){
		var pointsTag = document.createElement("sup");
		pointsTag.innerHTML = "+"+points;
		pointsTag.className = "reached-"+length;

		var toDisplay = document.getElementById("woot");
		toDisplay.appendChild(pointsTag);
	}
}
