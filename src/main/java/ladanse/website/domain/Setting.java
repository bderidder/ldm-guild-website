package ladanse.website.domain;

import javax.persistence.*;

@Entity
@Table(name = "Setting")
public class Setting
{
   private Long id;
   private String key;
   private String value;
   private Account account;

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
   @JoinColumn(name = "account", nullable = false)
   public Account getAccount()
   {
	 return account;
   }

   public void setAccount(Account account)
   {
	 this.account = account;
   }
}
