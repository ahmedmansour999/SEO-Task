# SEO Task


# PostMan Collection 
- in repo Files (SEO.postman_collection.json)
    https://github.com/ahmedmansour999/SEO-Task/blob/8ab29e97a6ca09447f10317a0eefc99a765121d2/README.md#L6

# DataBase Name is (SEO)

## Requirements
- PHP 7.4+
- Composer
- Node.js
- MySQL 
- Mailtrap account
- twilio account

## Content 
    - For Auth  (Using JwtAuth )
        -> Log in 
            using (email or phone Number) and password
            Required (identifier - Password)

        -> Register
            require (name - email -phone - password )
            is admin = 0 default
            send verify sms to active account (using twilio )

        -> Logout
    
    - User Details
        -> my profile (required admin token )
            get auth user data

        -> User (required user token)
                get all user
                get one user
                update user
                delete user

    - Post ( require Token )
            get All posts
            get one post
            create post
            update post
            delete post

##   command to send daily report for new user and new post ( php artisan daily:report)



### Clone the Repository
```bash
git clone https://github.com/ahmedmansour999/SEO-Task.git


