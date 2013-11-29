package ladanse.website.domain;

import javax.persistence.*;
import java.util.Date;

@Entity
@Table(name = "LoginEvent")
public class LoginEvent
{
   private Long id;
   private Date loginTime;
   private Account account;

   protected LoginEvent()
   {
   }

   public LoginEvent(Account account)
   {
	 setLoginTime(new Date());
	 setAccount(account);
   }

   @Id
   @Column(name = "id")
   @GeneratedValue(strategy = GenerationType.IDENTITY)
   public Long getId()
   {
	 return id;
   }

   protected void setId(Long id)
   {
	 this.id = id;
   }

   @Basic
   @Column(name = "loginTime")
   @Temporal(TemporalType.TIMESTAMP)
   public Date getLoginTime()
   {
	 return loginTime;
   }

   protected void setLoginTime(Date loginTime)
   {
	 this.loginTime = loginTime;
   }

   @ManyToOne
   @JoinColumn(name = "account", nullable = false)
   public Account getAccount()
   {
	 return account;
   }

   protected void setAccount(Account account)
   {
	 this.account = account;
   }
}
