export class CustomOverlay extends google.maps.OverlayView
{
	constructor(latLng, customHTML) {
		super();
		this.latLng = latLng;
		this.customHTML = customHTML;

		// Define a property to hold the image's div. We'll
		// actually create this div upon receipt of the onAdd()
		// method so we'll leave it null for now.
		this.div_ = null;
	}

	/**
	 * onAdd is called when the map's panes are ready and the overlay has been
	 * added to the map.
	 */
	onAdd() {
		this.div_ = document.createElement("div");
		this.div_.className = "customOverlay";
		this.div_.innerHTML = this.customHTML;		

		// Add the element to the "overlayLayer" pane.
		const panes = this.getPanes();
		panes.overlayMouseTarget.appendChild(this.div_);

		var me = this;
		this.div_.addEventListener('click', function(event) {
			google.maps.event.trigger(me, 'click');
			event.stopPropagation();
		});

		this.div_.addEventListener('touchstart', function(event) {
			google.maps.event.trigger(me, 'click');
			event.stopPropagation();
		});

		this.div_.addEventListener('mouseover', function(event) {
			google.maps.event.trigger(me, 'mouseover');
			event.stopPropagation();
		});

		this.div_.addEventListener('mouseout', function(event) {
			google.maps.event.trigger(me, 'mouseout');
			event.stopPropagation();
		});

		this.div_.addEventListener('dblclick', function(event) {
			event.stopPropagation();
		});
	}

	draw() {
		// We use the south-west and north-east
		// coordinates of the overlay to peg it to the correct position and size.
		// To do this, we need to retrieve the projection from the overlay.
		const overlayProjection = this.getProjection();
		let position = overlayProjection.fromLatLngToDivPixel(this.latLng);

		if (this.div_) {
			this.div_.style.left = position.x - 30 + "px";
			this.div_.style.top = position.y + "px";
			this.div_.style.position = 'absolute';
    		this.div_.style.cursor = 'pointer';
		}
	}

	/**
	* The onRemove() method will be called automatically from the API if
	* we ever set the overlay's map property to 'null'.
	*/
	onRemove() {
		if (this.div_) {
			this.div_.parentNode.removeChild(this.div_);
			this.div_ = null;
		}
	}

	/**
	 *  Set the visibility to 'hidden' or 'visible'.
	 */
	hide() {
		if (this.div) {
			this.div.style.visibility = "hidden";
		}
	}

	show() {
		if (this.div) {
			this.div.style.visibility = "visible";
		}
	}

    toggle() {
		if (this.div) {
			if (this.div.style.visibility === "hidden") {
				this.show();
			}
			else {
				this.hide();
			}
		}
    }

	toggleDOM(map) {
		if (this.getMap()) {
			this.setMap(null);
		}
		else {
			this.setMap(map);
		}
	}
}