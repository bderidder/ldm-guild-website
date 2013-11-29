package ladanse.website.domain;

import javax.persistence.*;

@Entity
@Table(name = "UserSetting")
public class UserSetting
{
   private Long id;
   private String key;
   private String value;
   private Account user;

   @Id
   @Column(name = "id")
   @GeneratedValue(strategy = GenerationType.IDENTITY)
   public Long getId()
   {
	 return id;
   }

   public void setId(Long id)
   {
	 this.id = id;
   }

   @Basic
   @Column(name = "key")
   public String getKey()
   {
	 return key;
   }

   public void setKey(String key)
   {
	 this.key = key;
   }

   @Basic
   @Column(name = "value")
   public String getValue()
   {
	 return value;
   }

   public void setValue(String value)
   {
	 this.value = value;
   }

   @ManyToOne
   @JoinColumn(name = "user", nullable = false)
   public Account getUser()
   {
	 return user;
   }

   public void setUser(Account user)
   {
	 this.user = user;
   }
}
