function table(table,resource,table_headers,safe_index)
{
	var resulset = null;
	
	this.table = table;	
	
	this.resource = resource;
	
	this.table_headers = table_headers;
	
	this.safe_table = safe_index;
	
	this.init();
	
	this.test = function()
	{
		this.resource.test(this);
	}
	this.rows = function()
	{	
		if(resulset == null)
		{
			var request = this.resource.getAll(this);
			request.then(function(response){
				 resulset = response.data.rows;			
			})
			
		}
		else
		{
			return resulset;
		}
				
	}
}

table.prototype.create = function(row,copy){
	var last = this.table_body[this.table_body.length - 1];
	console.log(last);
	
	row.id = (parseInt(last.id)+parseInt(1)).toString();	
	
	copy = angular.copy(row);
	
	this.table_body.push(copy);
	
	copy = {};
	
	this.data = row;
	
	var request = this.resource.create(this);
	request.then(function(response){
		if(response.data.status == 1)
		{
			alert("creado");
			newelement = {};
		}
		
	});
   
};

table.prototype.update = function(row){
   this.data = JSON.stringify(row, function (key, val) {
		 if (key == '$$hashKey') {
		   return undefined;
		 }
		 return val;
	});

	this.data = angular.fromJson(this.data);		
	var request = this.resource.persist(this);
	request.then(function(response){
		if(response.data.status == 1)
		{
			alert("Editado");
		}
	});
};

table.prototype.delete = function(row)
{		
	this.data = angular.fromJson(row);	
	var request = this.resource.delete(this);
	var object = this;
	request.then(function(response){
		//console.log(object);
		
		if(response.data.status == 1)
		{
			var index = object.table_body.indexOf(row);
			object.table_body.splice(index, 1);
			alert("eliminado");
		}
		if(response.data.status == 2)
		{
			alert(response.data.message);
		}
		
	});	
};		

table.prototype.init = function()
{
	var self = this;
	var request = this.resource.getAll(self);
	request.then(function(response){
		 self.table_body = response.data.rows;			
	});		
}
	



var app = angular.module("Gapp",["ngTable"]);