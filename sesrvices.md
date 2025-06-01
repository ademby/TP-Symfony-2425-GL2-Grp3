plus ou maoins les service, ama chouf api 5atir fama hajet badalthoum


@ROLE_USER
	#CategoryService
		getCategories() -> [array of Categories]
	#ProuctService
		getProducts() -> [array of proucts]
		getProducts(category=null) -> [array of proucts filtered by one category]
		getProductProperties(product) -> assoc array -- if null return null, else conv from json
	#CartService
		getCart() (or null)
		addProduct(pid) -- add product to cart items
		removeProduct(pid) -- remove if exists to cart items
		incQte(pid) -- inc Qte
		decQte(pid) -- dec(positive) qte
		setQte(pid, new_qte) -- set
		getTotal() -- calculate total
		unpersist() -- del cart
		clear() -- remove all
		clearZeroQte() -- remve all zero qte items
		validate() => array{pid, qte, price} -- dump nonn zero content into a map and unpersist() an return assoc array
	#OrderService
		create({pid, qte, price})
		setStatus()
		sendMail(oder, MailService)
	#MailerService
		configure()
		setRecipient
		setRestination
		setMail(mail)
		send() -- builder pattern


@ROLE_ADMIN
	#CategoryService
		INHERIT
		addCategory(cat)
		updateCategory(id|cat, category)
		delCategory(id|cat)
	#ProductService
		INHERIT ROLE_USER
		addProduct(p)
		updateProduct(id, p)
		delProduct(p)
		replaceCategory(?cat) -- can set and unset
	#CartService
		-- none admin does not control cart
	#Order Service
		setStatus(new_stat)
		del()
	#UploadService
		upload(file, ?validator) => url
		copy(file|url, dir|url) => url
		
		
		
	 
	
		
		
		
		
		
	
