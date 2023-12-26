<div class="login container row justify-content-center align-items-center">
    <div class="logimg col-xl-7 my-5 d-flex justify-content-center align-items-center">
        <img src="./assets/images/new logo.png" alt="ezServe Logo">
    </div>

    <div class="card justify-content-between border-0 col-sm-8 col-xl-5 shadow p-3 mb-5 bg-white rounded">
        <div class="card-body">
            <h1>Sign Up</h1>
            <hr>
            <form method="post">
              <input type="hidden" name="action" value="register_user">
              <div class="row">
                  <p class="col-sm-4">Sign up as</p>
                  <select class="select col-sm-4 mb-3" name="data[usertype]" required>
                      <option value="" selected disabled>Select</option>
                      <option value="client">Client</option>
                      <option value="business owner">Business Owner</option>
                  </select>
              </div>
                        
              <!-- DEFAULT FORM FIELD -->
              <div class="mb-2">
                  <label for="exampleInputEmail1" class="form-label">First Name</label>
                  <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="data[fname]">			
              </div>
              <div class="mb-2">
                  <label for="exampleInputEmail1" class="form-label">Last Name</label>
                  <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="data[lname]">			
              </div>
              <div class="mb-2">
                  <label for="exampleInputEmail1" class="form-label">Birthday</label>
                  <input type="date" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="data[birthday]">			
              </div>
              <div class="mb-2"> 
                  <label for="exampleInputEmail1" class="form-label">Email</label>
                  <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="data[email]">			
              </div>
              <div class="mb-2">
                  <label for="exampleInputEmail1" class="form-label">Mobile Number</label>
                  <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="data[number]">			
              </div>
              <div class="mb-2">
                  <label for="exampleInputPassword1" class="form-label">Username</label>
                <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="data[username]">	
              </div>
              <div class="mb-2">
                  <label for="exampleInputPassword1" class="form-label">Password</label>
                  <input type="password" class="form-control" id="exampleInputPassword1" name="data[password]">
              </div>
        
              <button type="submit" class="btn btn-primary">Register</button>        
            </form>
          </div>
        </div>
      </div>
