class RouleteForPublicEvents {
	chart = {};
	pieSeries = {};
	signedParameters = '';


	constructor(signedParameters) {
		this.signedParameters = signedParameters;
	}

	closeWindow(e) {
		e.preventDefault();
		document.querySelector('.roulete-container').style.display = "none";
		return false;
	}

	roll() {
		document.querySelector('#roll-btn').style.display = "none";
		document.querySelector('.roulete-order-count').style.display = "none";
		document.querySelector('.again').style.display = "none";

		BX.ajax.runComponentAction("clanbeauty:roulete.for.public.events", 'getRouleteResult', {
			mode: 'class',
			signedParameters: this.signedParameters,
			data: {userName: document.querySelector('#userName').value}
		}).then((response) => {
			if (response.data.id) {
				this.rotateRoulete(response.data.id);
				window.setTimeout(() => {
					this.showMessage(response.data.status, response.data.message);
					if (parseInt(response.data.prizesLeft) > 0) {
						document.querySelector('.again').style.display = "block";
					}
				}, 6000);
			} else {
				this.showMessage(response.data.status, response.data.message)
			}
		});
	}
	rotateRoulete(id) {
		let wonSlice = {};

		this.pieSeries.slices.each(function (slice) {
			if (slice.dataItem.id == id) {
				wonSlice = slice;
			}
		});

		if (wonSlice) {
			let angle = -90 - wonSlice.startAngle - (wonSlice.arc / 2);
			angle += 360 * 10; //360 deg * 10 turns
			document.querySelector('#roulete-circle').style.transform = 'rotate(' + angle + 'deg)';
		}
	}

	showMessage(status, message) {
		let messageEl = document.querySelector('#roulete-message');
		messageEl.style.display = "block";
		messageEl.classList.add(status);
		messageEl.innerHTML = message;
	}

	initChart(prizes) {
		am4core.options.classNamePrefix = "clanRoulete-";

		am4core.options.autoSetClassName = true;

		this.chart = am4core.create("roulete-circle", am4charts.PieChart);
		this.chart.radius = am4core.percent(100);

		this.chart.logo.disabled = true;
		this.chart.data = prizes;

		this.pieSeries = this.chart.series.push(new am4charts.PieSeries());
		this.pieSeries.dataFields.value = 'size';
		this.pieSeries.dataFields.category = "name";
		this.pieSeries.dataFields.id = "id";

		this.pieSeries.tooltip.disabled = true;
		this.pieSeries.tooltip.opacity = 1;
		this.pieSeries.ticks.template.disabled = true;
		this.pieSeries.alignLabels = false;
		this.pieSeries.labels.template.fontsize = 12;
		this.pieSeries.labels.template.radius = -120; //px
		if(window.innerWidth < 768) {
			this.pieSeries.labels.template.fontsize = 10;
			this.pieSeries.labels.template.radius = -95; //px			
		}
		this.pieSeries.labels.template.fill = "text_color";
		this.pieSeries.labels.template.relativeRotation = 0;
		this.pieSeries.labels.template.maxWidth = 50;
		this.pieSeries.labels.template.padding(1, 1, 1, 1);

		let slice = this.pieSeries.slices.template;
		slice.propertyFields.fill = "color";
		slice.states.getKey("hover").properties.scale = 1;
		slice.states.getKey("active").properties.shiftRadius = 0;

		this.chart.events.on('ready', () => {
			this.pieSeries.labels.each((el) => {
				let image = el.createChild(am4core.Image);
				image.href = el.dataItem.dataContext.image;
				image.height = 64;
				image.width = 64;

				el.html = `<p>${el.dataItem.dataContext.name}</p><img src='${el.dataItem.dataContext.image}'/>`;
			});
			roulete.pieSeries.reinit();
			document.querySelector('.roulete-container').style = "display: block";
		});

	}

	randomIntFromInterval(min, max) { // min and max included 
		return Math.floor(Math.random() * (max - min + 1) + min)
	}
}