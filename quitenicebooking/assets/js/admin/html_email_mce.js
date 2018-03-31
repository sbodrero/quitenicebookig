(function() {
	tinymce.create('tinymce.plugins.quitenicebooking_premium', {
		init: function(ed, url) {
			// add buttons
			ed.addButton('guest_first_name', {
				title: 'Guest First Name',
				cmd: 'guest_first_name',
				image: url + '/../../images/guest_first_name.png'
			});
			ed.addButton('guest_last_name', {
				title: 'Guest Last Name',
				cmd: 'guest_last_name',
				image: url + '/../../images/guest_last_name.png'
			});
			ed.addButton('guest_email', {
				title: 'Guest Email',
				cmd: 'guest_email',
				image: url + '/../../images/guest_email.png'
			});
			ed.addButton('hotel_name', {
				title: 'Hotel Name',
				cmd: 'hotel_name',
				image: url + '/../../images/hotel_name.png'
			});
			ed.addButton('booking_id', {
				title: 'Booking ID',
				cmd: 'booking_id',
				image: url + '/../../images/booking_id.png'
			});
			ed.addButton('single_room', {
				title: 'Single Room Booking',
				cmd: 'single_room',
				image: url + '/../../images/single_room.png'
			});
			ed.addButton('multi_room', {
				title: 'Multi Room Booking',
				cmd: 'multi_room',
				image: url + '/../../images/multi_room.png'
			});
			ed.addButton('room_number', {
				title: 'Room Number',
				cmd: 'room_number',
				image: url + '/../../images/room_number.png'
			});
			ed.addButton('room_type', {
				title: 'Room Type',
				cmd: 'room_type',
				image: url + '/../../images/room_type.png'
			});
			ed.addButton('checkin', {
				title: 'Check In',
				cmd: 'checkin',
				image: url + '/../../images/checkin.png'
			});
			ed.addButton('checkout', {
				title: 'Check Out',
				cmd: 'checkout',
				image: url + '/../../images/checkout.png'
			});
			ed.addButton('num_guests', {
				title: 'Number of Guests',
				cmd: 'num_guests',
				image: url + '/../../images/num_guests.png'
			});
			ed.addButton('services', {
				title: 'Services',
				cmd: 'services',
				image: url + '/../../images/services.png'
			});
			ed.addButton('guest_details', {
				title: 'Guest Details',
				cmd: 'guest_details',
				image: url + '/../../images/guest_details.png'
			});
			ed.addButton('payment_deposit', {
				title: 'Deposit Due',
				cmd: 'payment_deposit',
				image: url + '/../../images/payment_deposit.png'
			});
			ed.addButton('payment_total', {
				title: 'Total Payment',
				cmd: 'payment_total',
				image: url + '/../../images/payment_total.png'
			});
			// add commands
			ed.addCommand('guest_first_name', function() {
				ed.execCommand('mceInsertContent', 0, '[guest_first_name]');
			});
			ed.addCommand('guest_last_name', function() {
				ed.execCommand('mceInsertContent', 0, '[guest_last_name]');
			});
			ed.addCommand('guest_email', function() {
				ed.execCommand('mceInsertContent', 0, '[guest_email]');
			});
			ed.addCommand('hotel_name', function() {
				ed.execCommand('mceInsertContent', 0, '[hotel_name]');
			});
			ed.addCommand('booking_id', function() {
				ed.execCommand('mceInsertContent', 0, '[booking_id]');
			});
			ed.addCommand('single_room', function() {
				ed.execCommand('mceInsertContent', 0, '[single_room_booking][/single_room_booking]');
			});
			ed.addCommand('multi_room', function() {
				ed.execCommand('mceInsertContent', 0, '[multi_room_booking][/multi_room_booking]');
			});
			ed.addCommand('room_number', function() {
				ed.execCommand('mceInsertContent', 0, '[room_number]');
			});
			ed.addCommand('room_type', function() {
				ed.execCommand('mceInsertContent', 0, '[room_type]');
			});
			ed.addCommand('checkin', function() {
				ed.execCommand('mceInsertContent', 0, '[checkin]');
			});
			ed.addCommand('checkout', function() {
				ed.execCommand('mceInsertContent', 0, '[checkout]');
			});
			ed.addCommand('num_guests', function() {
				ed.execCommand('mceInsertContent', 0, '[number_of_guests]');
			});
			ed.addCommand('services', function() {
				ed.execCommand('mceInsertContent', 0, '[services]');
			});
			ed.addCommand('guest_details', function() {
				ed.execCommand('mceInsertContent', 0, '[guest_details][/guest_details]');
			});
			ed.addCommand('payment_deposit', function() {
				ed.execCommand('mceInsertContent', 0, '[payment_deposit]');
			});
			ed.addCommand('payment_total', function() {
				ed.execCommand('mceInsertContent', 0, '[payment_total]');
			});
		},
		createControl: function(n, cm) {

		},
		getInfo: function() {
			return {
				longname: 'Quitenicebooking Premium Buttons',
				author: 'Quite Nice Stuff',
				authorurl: 'http://www.quitenicestuff.com/',
				infourl: 'http://www.quitenicestuff.com/',
				version: '2.5.0'
			};
		}
	});
	// register plugin
	tinymce.PluginManager.add('quitenicebooking_premium', tinymce.plugins.quitenicebooking_premium);
})();
